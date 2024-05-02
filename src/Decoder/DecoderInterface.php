<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Decoder;

use DomainException;
use InvalidArgumentException;
use JimTools\JwtAuth\Exceptions\BeforeValidException;
use JimTools\JwtAuth\Exceptions\ExpiredException;
use JimTools\JwtAuth\Exceptions\SignatureInvalidException;
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
