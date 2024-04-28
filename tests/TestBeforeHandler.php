<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 */
final class TestBeforeHandler
{
    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public function __invoke(
        ServerRequestInterface $request,
        array $arguments
    ): ServerRequestInterface {
        return $request->withAttribute('test', 'invoke');
    }

    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public static function before(
        ServerRequestInterface $request,
        array $arguments
    ): ServerRequestInterface {
        return $request->withAttribute('test', 'function');
    }
}
