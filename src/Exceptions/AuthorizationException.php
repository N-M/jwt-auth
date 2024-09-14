<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Exceptions;

class AuthorizationException
{
    /**
     * @codeCoverageIgnore
     */
    public static function noTokenFound(): self
    {
        return new self('Token not found.');
    }
}
