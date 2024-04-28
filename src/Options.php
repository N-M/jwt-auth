<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use Closure;

final class Options
{
    /**
     * @param string[] $relaxed
     */
    public function __construct(
        public readonly bool $isSecure = true,
        public readonly array $relaxed = ['localhost', '127.0.0.1', '::1'],
        public readonly string $header = 'Authorization',
        public readonly string $regexp = '/Bearer\\s+(.*)$/i',
        public readonly string $cookie = 'token',
        public readonly ?string $attribute = 'token',
        public ?Closure $before = null,
        public ?Closure $after = null,
        public ?Closure $error = null,
    ) {}
}
