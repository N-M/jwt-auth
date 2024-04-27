<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class TestAfterHandler
{
    public function __invoke(
        ResponseInterface $response,
        array $arguments
    ) {
        $response->getBody()->write(self::class);

        return $response->withHeader('X-Brawndo', 'plants crave');
    }

    public static function after(
        ResponseInterface $response,
        array $arguments
    ) {
        $response->getBody()->write(self::class);

        return $response->withHeader('X-Water', 'like from toilet?');
    }
}
