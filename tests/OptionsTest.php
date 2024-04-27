<?php

declare(strict_types=1);

namespace Tuupola\Middleware;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Options::class)]
final class OptionsTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $options = new Options();

        self::assertTrue($options->isSecure);
        self::assertSame(['localhost', '127.0.0.1', '::1'], $options->relaxed);
        self::assertSame('Authorization', $options->header);
        self::assertSame('/Bearer\\s+(.*)$/i', $options->regexp);
        self::assertSame('token', $options->cookie);
        self::assertSame('token', $options->attribute);
        self::assertNull($options->before);
        self::assertNull($options->after);
        self::assertNull($options->error);
    }
}
