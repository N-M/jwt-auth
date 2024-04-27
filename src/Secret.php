<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use SensitiveParameter;

final class Secret
{
    public function __construct(
        #[SensitiveParameter]
        public string $secret,
        public string $algorithm,
        public string|null $kid = null,
    ) {
    }
}