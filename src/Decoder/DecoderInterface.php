<?php

declare(strict_types=1);

namespace Tuupola\Middleware\Decoder;

use DomainException;
use InvalidArgumentException;
use Tuupola\Middleware\Exceptions\BeforeValidException;
use Tuupola\Middleware\Exceptions\ExpiredException;
use Tuupola\Middleware\Exceptions\SignatureInvalidException;
use UnexpectedValueException;

interface DecoderInterface
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws SignatureInvalidException
     * @throws BeforeValidException
     * @throws ExpiredException
     * @throws UnexpectedValueException
     */
    public function decode(string $jwt): array;
}
