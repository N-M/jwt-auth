<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Test;

use DateTime;
use DomainException;
use Firebase\JWT\JWT;
use Generator;
use InvalidArgumentException;
use JimTools\JwtAuth\Decoder\DecoderInterface;
use JimTools\JwtAuth\Decoder\FirebaseDecoder;
use JimTools\JwtAuth\Exceptions\BeforeValidException;
use JimTools\JwtAuth\Exceptions\ExpiredException;
use JimTools\JwtAuth\Exceptions\SignatureInvalidException;
use JimTools\JwtAuth\Secret;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * @internal
 */
#[CoversClass(FirebaseDecoder::class), UsesClass(Secret::class)]
final class FirebaseDecoderTest extends TestCase
{
    private DecoderInterface $decoder;

    #[Override]
    protected function setUp(): void
    {
        $this->decoder = new FirebaseDecoder(new Secret('tooManySecrets', 'HS256', 'acme'));
    }

    public function testInvalidArgumentExceptionIsThrown(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseDecoder())->decode('..');
    }

    public static function malformedProvider(): Generator
    {
        yield 'empty' => [''];

        yield 'malformed' => ['x.y'];
    }

    #[DataProvider('malformedProvider')]
    public function testUnexpectedValueExceptionIsThrown(string $token): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->decoder->decode($token);
    }

    public function testDomainExceptionIsThrown(): void
    {
        $this->expectException(DomainException::class);
        $this->decoder->decode('x.y.z');
    }

    public function testSignatureInvalidExceptionIsThrown(): void
    {
        $this->expectException(SignatureInvalidException::class);

        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImFjbWUifQ.eyJpYXQiOjE1MTYyMzkwMjJ9.nope';
        $this->decoder->decode($token);
    }

    public static function beforeValidProvider(): Generator
    {
        yield 'nbf is in the future' => [['iat' => time(), 'nbf' => (new DateTime('+5 minutes'))->getTimestamp()]];

        yield 'iat is in the future' => [['iat' => (new DateTime('+5 minutes'))->getTimestamp()]];
    }

    /**
     * @param array<string, mixed> $payload
     */
    #[DataProvider('beforeValidProvider')]
    public function testBeforeValidExceptionIsThrown(array $payload): void
    {
        $this->expectException(BeforeValidException::class);
        $token = $this->encode($payload);

        $this->decoder->decode($token);
    }

    public function testExpiredExceptionIsThrown(): void
    {
        $this->expectException(ExpiredException::class);
        $token = $this->encode(['exp' => (new DateTime('-5 minutes'))->getTimestamp()]);

        $this->decoder->decode($token);
    }

    public function testHappyPath(): void
    {
        $now = time();
        $token = JWT::encode(['iat' => $now], 'tooManySecrets', 'HS256', null, []);
        $decoded = (new FirebaseDecoder(new Secret('tooManySecrets', 'HS256')))->decode($token);

        self::assertSame($now, $decoded['iat']);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function encode(array $payload): string
    {
        return JWT::encode($payload, 'tooManySecrets', 'HS256', 'acme');
    }
}
