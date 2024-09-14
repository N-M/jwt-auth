<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Exceptions;

use RuntimeException;

class AuthorizationException extends RuntimeException
{
    /**
     * @codeCoverageIgnore
     */
    public static function noTokenFound(): self
    {
        return new self('Token not found.');
    }
}
