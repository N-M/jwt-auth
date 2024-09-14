<?php

declare(strict_types=1);

namespace JimTools\JwtAuth;

use JimTools\JwtAuth\Handlers\AfterHandlerInterface;
use JimTools\JwtAuth\Handlers\BeforeHandlerInterface;

/**
 * @codeCoverageIgnore
 */
final class Options
{
    /**
     * @param string[] $relaxed
     */
    public function __construct(
        public readonly bool $isSecure = true,
        public readonly array $relaxed = ['localhost', '127.0.0.1', '::1'],
        public readonly string $header = 'Authorization',
        public readonly string $regexp = '/Bearer\s+(.*)$/i',
        public readonly string $cookie = 'token',
        public readonly ?string $attribute = 'token',
        public ?BeforeHandlerInterface $before = null,
        public ?AfterHandlerInterface $after = null,
    ) {}
}
