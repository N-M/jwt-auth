<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Rules;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Rule to decide by request path whether the request should be authenticated or not.
 */
final class RequestPathRule implements RuleInterface
{
    /**
     * @param array<string> $paths
     * @param array<string> $ignore
     */
    public function __construct(
        private readonly array $paths = ['/'],
        private readonly array $ignore = [],
    ) {}

    public function __invoke(ServerRequestInterface $request): bool
    {
        $uri = '/' . $request->getUri()->getPath();
        $uri = preg_replace('#/+#', '/', $uri);

        // If request path is matches ignore should not authenticate.
        foreach ($this->ignore as $ignore) {
            $ignore = rtrim($ignore, '/');
            if ((bool) preg_match("@^{$ignore}(/.*)?$@", (string) $uri)) {
                return false;
            }
        }

        // Otherwise check if path matches and we should authenticate.
        foreach ($this->paths as $path) {
            $path = rtrim($path, '/');
            if ((bool) preg_match("@^{$path}(/.*)?$@", (string) $uri)) {
                return true;
            }
        }

        return false;
    }
}
