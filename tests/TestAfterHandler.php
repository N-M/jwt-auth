<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class TestAfterHandler
{
    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public function __invoke(
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        $response->getBody()->write(self::class);

        return $response->withHeader('X-Brawndo', 'plants crave');
    }

    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public static function after(
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        $response->getBody()->write(self::class);

        return $response->withHeader('X-Water', 'like from toilet?');
    }
}
