<?php

declare(strict_types=1);

namespace Tuupola\Middleware\Decoder;

interface DecoderInterface
{
    public function decode(string $jwt): array;
}
