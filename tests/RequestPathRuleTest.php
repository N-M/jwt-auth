<?php

namespace JimTools\JwtAuth\JwtAuthentication;

use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RequestPathRule::class)]
final class RequestPathRuleTest extends TestCase
{
    public function testShouldAcceptArrayAndStringAsPath(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        $rule = new RequestPathRule(['/api']);
        self::assertTrue($rule($request));

        $rule = new RequestPathRule(['/api', '/foo']);
        self::assertTrue($rule($request));
    }

    public function testShouldAuthenticateEverything(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/'
        );

        $rule = new RequestPathRule();
        self::assertTrue($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        self::assertTrue($rule($request));
    }

    public function testShouldAuthenticateOnlyApi(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/'
        );

        $rule = new RequestPathRule(['/api']);
        self::assertFalse($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        self::assertTrue($rule($request));
    }

    public function testShouldIgnoreLogin(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        $rule = new RequestPathRule(['/api'], ['/api/login']);
        self::assertTrue($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api/login'
        );

        self::assertFalse($rule($request));
    }

    public function testShouldAuthenticateCreateAndList(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api'
        );

        // Should not authenticate
        $rule = new RequestPathRule(['/api/create', '/api/list']);
        self::assertFalse($rule($request));

        // Should authenticate
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api/create'
        );

        self::assertTrue($rule($request));

        // Should authenticate
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api/list'
        );

        self::assertTrue($rule($request));

        // Should not authenticate
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api/ping'
        );

        self::assertFalse($rule($request));
    }

    public function testShouldAuthenticateRegexp(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api/products/123/tickets/anything'
        );

        // Should authenticate
        $rule = new RequestPathRule(['/api/products/(\\d*)/tickets']);
        self::assertTrue($rule($request));

        // Should not authenticate
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/api/products/xxx/tickets'
        );

        self::assertFalse($rule($request));
    }

    public function testBug50ShouldAuthenticateMultipleSlashes(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/'
        );

        $rule = new RequestPathRule(['/v1/api']);
        self::assertFalse($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/v1/api'
        );

        self::assertTrue($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/v1//api'
        );

        self::assertTrue($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com/v1//////api'
        );

        self::assertTrue($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com//v1/api'
        );

        self::assertTrue($rule($request));

        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com//////v1/api'
        );

        self::assertTrue($rule($request));
    }
}
