<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Decoder;

use JimTools\JwtAuth\Exceptions\BeforeValidException;
use JimTools\JwtAuth\Exceptions\DomainException;
use JimTools\JwtAuth\Exceptions\ExpiredException;
use JimTools\JwtAuth\Exceptions\InvalidArgumentException;
use JimTools\JwtAuth\Exceptions\SignatureInvalidException;
use JimTools\JwtAuth\Exceptions\UnexpectedValueException;

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
