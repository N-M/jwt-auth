<?php

declare(strict_types=1);

namespace JimTools\JwtAuth;

use SensitiveParameter;

final class Secret
{
    public function __construct(
        #[SensitiveParameter]
        public string $secret,
        public string $algorithm,
        public ?string $kid = null,
    ) {}
}
