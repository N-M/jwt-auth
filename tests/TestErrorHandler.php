<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class TestErrorHandler
{
    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public function __invoke(
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        $response->getBody()->write(self::class);

        return $response
            ->withStatus(402)
            ->withHeader('X-Foo', 'Bar');
    }

    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public static function error(
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        $response->getBody()->write(self::class);

        return $response
            ->withStatus(418)
            ->withHeader('X-Bar', 'Foo');
    }
}
