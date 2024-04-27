<?php

declare(strict_types=1);

namespace Tuupola\Middleware\JwtAuthentication;

use Psr\Http\Message\ServerRequestInterface;

use function in_array;

/**
 * Rule to decide by HTTP verb whether the request should be authenticated or not.
 */
final class RequestMethodRule implements RuleInterface
{
    /**
     * Stores all the options passed to the rule.
     *
     * @var array{
     *   ignore: array<string>,
     * }
     */
    private array $options = [
        'ignore' => ['OPTIONS'],
    ];

    /**
     * @param array{
     *   ignore?: array<string>,
     * } $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function __invoke(ServerRequestInterface $request): bool
    {
        return !in_array($request->getMethod(), $this->options['ignore'], true);
    }
}
