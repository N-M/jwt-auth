<?php

declare(strict_types=1);

namespace Tuupola\Middleware\Decoder;

interface DecoderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function decode(string $jwt): array;
}
