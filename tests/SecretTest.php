<?php

declare(strict_types=1);

namespace Tuupola\Middleware\JwtAuthentication;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tuupola\Middleware\Secret;

/**
 * @internal
 */
#[CoversClass(Secret::class)]
final class SecretTest extends TestCase
{
    public function testDtoValues(): void
    {
        $secret = new Secret('tooManySecrets', 'HS256', 'acme');

        self::assertSame('tooManySecrets', $secret->secret);
        self::assertSame('HS256', $secret->algorithm);
        self::assertSame('acme', $secret->kid);
    }
}
