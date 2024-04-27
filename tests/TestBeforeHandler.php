<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 */
final class TestBeforeHandler
{
    public function __invoke(
        ServerRequestInterface $request,
        array $arguments
    ) {
        return $request->withAttribute('test', 'invoke');
    }

    public static function before(
        ServerRequestInterface $request,
        array $arguments
    ) {
        return $request->withAttribute('test', 'function');
    }
}
