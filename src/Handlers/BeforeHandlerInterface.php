<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Handlers;

use Psr\Http\Message\ServerRequestInterface;

interface BeforeHandlerInterface
{
    /**
     * @param array{decoded: array<string, mixed>, token: string} $arguments
     */
    public function __invoke(ServerRequestInterface $request, array $arguments): ServerRequestInterface;
}
