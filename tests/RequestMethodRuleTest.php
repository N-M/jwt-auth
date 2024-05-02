<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Test;

use JimTools\JwtAuth\Rules\RequestMethodRule;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RequestMethodRule::class)]
final class RequestMethodRuleTest extends TestCase
{
    public function testShouldNotAuthenticateOptions(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'OPTIONS',
            'https://example.com/api'
        );

        $rule = new RequestMethodRule();

        self::assertFalse($rule($request));
    }

    public function testShouldAuthenticatePost(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'POST',
            'https://example.com/api'
        );

        $rule = new RequestMethodRule();

        self::assertTrue($rule($request));
    }

    public function testShouldAuthenticateGet(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        $rule = new RequestMethodRule();

        self::assertTrue($rule($request));
    }

    public function testShouldConfigureIgnore(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        $rule = new RequestMethodRule(['GET']);

        self::assertFalse($rule($request));
    }
}
