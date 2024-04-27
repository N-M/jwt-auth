<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use ArrayAccess;
use Closure;
use DomainException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use SplStack;
use Tuupola\Http\Factory\ResponseFactory;
use Tuupola\Middleware\JwtAuthentication\RequestMethodRule;
use Tuupola\Middleware\JwtAuthentication\RequestPathRule;
use Tuupola\Middleware\JwtAuthentication\RuleInterface;

use function array_fill_keys;
use function array_keys;
use function call_user_func;
use function count;
use function in_array;
use function is_array;
use function is_callable;
use function is_string;

final class JwtAuthentication implements MiddlewareInterface
{
    use DoublePassTrait;

    /**
     * The rules stack.
     *
     * @var SplStack<RuleInterface>
     */
    private SplStack $rules;

    /**
     * @param Secret[]        $secrets
     * @param RuleInterface[] $rules
     */
    public function __construct(
        private readonly Options $options,
        private array $secrets,
        ?array $rules = null,
        private ?LoggerInterface $logger = null
    ) {
        foreach ($secrets as $secret) {
            if (!$secret instanceof Secret) {
                throw new InvalidArgumentException('balls');
            }
        }

        // Setup stack for rules
        $this->rules = new SplStack();
        if ($rules === null) {
            $this->rules->push(new RequestMethodRule());
            $this->rules->push(new RequestPathRule());
        } else {
            foreach ($rules as $rule) {
                $this->rules->push($rule);
            }
        }

        if ($options->before !== null) {
            $this->before($options->before);
        }

        if ($options->after !== null) {
            $this->after($options->after);
        }

        if ($options->error !== null) {
            $this->error($options->error);
        }
    }

    /**
     * Process a request in PSR-15 style and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();

        // If rules say we should not authenticate call next and return.
        if (false === $this->shouldAuthenticate($request)) {
            return $handler->handle($request);
        }

        // HTTP allowed only if secure is false or server is in relaxed array.
        if ('https' !== $scheme && true === $this->options->isSecure) {
            if (!in_array($host, $this->options->relaxed, true)) {
                $message = sprintf(
                    'Insecure use of middleware over %s denied by configuration.',
                    strtoupper($scheme)
                );

                throw new RuntimeException($message);
            }
        }

        // If token cannot be found or decoded return with 401 Unauthorized.
        try {
            $token = $this->fetchToken($request);
            $decoded = $this->decodeToken($token);
        } catch (DomainException|RuntimeException $exception) {
            $response = (new ResponseFactory())->createResponse(401);

            return $this->processError($response, [
                'message' => $exception->getMessage(),
                'uri' => (string) $request->getUri(),
            ]);
        }

        $params = [
            'decoded' => $decoded,
            'token' => $token,
        ];

        // Add decoded token to request as attribute when requested.
        if ($this->options->attribute) {
            $request = $request->withAttribute($this->options->attribute, $decoded);
        }

        // Modify $request before calling next middleware.
        $before = $this->options->before;
        if (is_callable($before)) {
            $beforeRequest = $before($request, $params);
            if ($beforeRequest instanceof ServerRequestInterface) {
                $request = $beforeRequest;
            }
        }

        // Everything ok, call next middleware.
        $response = $handler->handle($request);

        // Modify $response before returning.
        $after = $this->options->after;
        if (is_callable($after)) {
            $afterResponse = $after($response, $params);
            if ($afterResponse instanceof ResponseInterface) {
                return $afterResponse;
            }
        }

        return $response;
    }

    /**
     * Set all rules in the stack.
     *
     * @param RuleInterface[] $rules
     */
    public function withRules(array $rules): self
    {
        $new = clone $this;
        // Clear the stack
        $new->rules = null;
        $new->rules = new SplStack();
        // Add the rules
        foreach ($rules as $callable) {
            $new = $new->addRule($callable);
        }

        return $new;
    }

    /**
     * Add a rule to the stack.
     */
    public function addRule(callable $callable): self
    {
        $new = clone $this;
        $new->rules = clone $this->rules;
        $new->rules->push($callable);

        return $new;
    }

    /**
     * Check if middleware should authenticate.
     */
    private function shouldAuthenticate(ServerRequestInterface $request): bool
    {
        // If any of the rules in stack return false will not authenticate
        foreach ($this->rules as $callable) {
            if (false === $callable($request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Call the error handler if it exists.
     *
     * @param mixed[] $arguments
     */
    private function processError(ResponseInterface $response, array $arguments): ResponseInterface
    {
        $error = $this->options->error;
        if (is_callable($error)) {
            $handlerResponse = $error($response, $arguments);
            if ($handlerResponse instanceof ResponseInterface) {
                return $handlerResponse;
            }
        }

        return $response;
    }

    /**
     * Fetch the access token.
     */
    private function fetchToken(ServerRequestInterface $request): string
    {
        // Check for token in header.
        $header = $request->getHeaderLine($this->options->header);

        if (false === empty($header)) {
            if (preg_match($this->options->regexp, $header, $matches)) {
                $this->log(LogLevel::DEBUG, 'Using token from request header');

                return $matches[1];
            }
        }

        // Token not found in header try a cookie.
        $cookieParams = $request->getCookieParams();

        if (isset($cookieParams[$this->options->cookie])) {
            $this->log(LogLevel::DEBUG, 'Using token from cookie');
            if (preg_match($this->options->regexp, $cookieParams[$this->options->cookie], $matches)) {
                return $matches[1];
            }

            return $cookieParams[$this->options->cookie];
        }

        // If everything fails log and throw.
        $this->log(LogLevel::WARNING, 'Token not found');

        throw new RuntimeException('Token not found.');
    }

    /**
     * Decode the token.
     *
     * @return mixed[]
     */
    private function decodeToken(string $token): array
    {
        $keys = [];
        foreach ($this->secrets as $secret) {
            $key = new Key($secret->secret, $secret->algorithm);

            if ($secret->kid === null) {
                $keys[] = $key;
            } else {
                $keys[$secret->kid] = $key;
            }
        }

        try {
            $decoded = JWT::decode(
                $token,
                $keys,
            );

            return (array) $decoded;
        } catch (Exception $exception) {
            $this->log(LogLevel::WARNING, $exception->getMessage(), [$token]);

            throw $exception;
        }
    }

    /**
     * Hydrate options from given array.
     *
     * @param mixed[] $data
     */
    private function hydrate(array $data = []): void
    {
        $data['algorithm'] = $data['algorithm'] ?? $this->options['algorithm'];
        if ((is_array($data['secret']) || $data['secret'] instanceof ArrayAccess)
            && is_array($data['algorithm'])
            && count($data['algorithm']) === 1
            && count((array) $data['secret']) > count($data['algorithm'])
        ) {
            $secretIndex = array_keys((array) $data['secret']);
            $data['algorithm'] = array_fill_keys($secretIndex, $data['algorithm'][0]);
        }

        foreach ($data as $key => $value) {
            // https://github.com/facebook/hhvm/issues/6368
            $key = str_replace('.', ' ', $key);
            $method = lcfirst(ucwords($key));
            $method = str_replace(' ', '', $method);
            if (method_exists($this, $method)) {
                // Try to use setter
                // @phpstan-ignore-next-line
                call_user_func([$this, $method], $value);
            } else {
                // Or fallback to setting option directly
                $this->options[$key] = $value;
            }
        }
    }

    /**
     * Set path where middleware should bind to.
     *
     * @param string|string[] $path
     */
    private function path($path): void
    {
        $this->options['path'] = (array) $path;
    }

    /**
     * Set path which middleware ignores.
     *
     * @param string|string[] $ignore
     */
    private function ignore($ignore): void
    {
        $this->options['ignore'] = (array) $ignore;
    }

    /**
     * Set the cookie name where to search the token from.
     */
    private function cookie(string $cookie): void
    {
        $this->options['cookie'] = $cookie;
    }

    /**
     * Set the secure flag.
     */
    private function secure(bool $secure): void
    {
        $this->options['secure'] = $secure;
    }

    /**
     * Set hosts where secure rule is relaxed.
     *
     * @param string[] $relaxed
     */
    private function relaxed(array $relaxed): void
    {
        $this->options['relaxed'] = $relaxed;
    }

    /**
     * Set the secret key.
     *
     * @param string|string[] $secret
     */
    private function secret($secret): void
    {
        if (false === is_array($secret) && false === is_string($secret) && !$secret instanceof ArrayAccess) {
            throw new InvalidArgumentException(
                'Secret must be either a string or an array of "kid" => "secret" pairs'
            );
        }
        $this->options['secret'] = $secret;
    }

    /**
     * Set the error handler.
     */
    private function error(callable $error): void
    {
        if ($error instanceof Closure) {
            $this->options->error = $error->bindTo($this);
        } else {
            $this->options->error = $error;
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed[] $context
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Set the before handler.
     */
    private function before(callable $before): void
    {
        if ($before instanceof Closure) {
            $this->options->before = $before->bindTo($this);
        } else {
            $this->options->before = $before;
        }
    }

    /**
     * Set the after handler.
     */
    private function after(callable $after): void
    {
        if ($after instanceof Closure) {
            $this->options->after = $after->bindTo($this);
        } else {
            $this->options->after = $after;
        }
    }

    /**
     * Set the rules.
     *
     * @param RuleInterface[] $rules
     */
    private function rules(array $rules): void
    {
        foreach ($rules as $callable) {
            $this->rules->push($callable);
        }
    }

    /**
     * @return array<string,Key>
     */
    private function createKeysFromAlgorithms(): array
    {
        if (!isset($this->options['secret'])) {
            throw new InvalidArgumentException(
                'Secret must be either a string or an array of "kid" => "secret" pairs'
            );
        }

        $keyObjects = [];
        foreach ($this->options['algorithm'] as $kid => $algorithm) {
            $keyId = !is_numeric($kid) ? $kid : $algorithm;

            $secret = $this->options['secret'];

            if (is_array($secret) || $secret instanceof ArrayAccess) {
                $secret = $this->options['secret'][$kid];
            }

            $keyObjects[$keyId] = new Key($secret, $algorithm);
        }

        return $keyObjects;
    }
}
