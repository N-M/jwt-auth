<?php

declare(strict_types=1);

namespace Tuupola\Middleware\Decoder;

use DomainException;
use Firebase\JWT\BeforeValidException as JwtBeforeValidException;
use Firebase\JWT\ExpiredException as JwtExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException as JwtSignatureInvalidException;
use InvalidArgumentException;
use Tuupola\Middleware\Exceptions\BeforeValidException;
use Tuupola\Middleware\Exceptions\ExpiredException;
use Tuupola\Middleware\Exceptions\SignatureInvalidException;
use Tuupola\Middleware\Secret;
use UnexpectedValueException;

use function count;

final class FirebaseDecoder implements DecoderInterface
{
    /**
     * @var key|Key[]
     */
    private array|Key $keys = [];

    /**
     * @param Secret[] $secrets
     */
    public function __construct(Secret ...$secrets)
    {
        foreach ($secrets as $secret) {
            $key = new Key($secret->secret, $secret->algorithm);
            if ($secret->kid === null) {
                $this->keys[] = $key;
            } else {
                $this->keys[$secret->kid] = $key;
            }
        }

        if (count($this->keys) === 1) {
            $this->keys = current($this->keys);
        }
    }

    public function decode(string $jwt): array
    {
        try {
            return (array) JWT::decode($jwt, $this->keys);
        } catch (InvalidArgumentException $e) {
            throw $e;
            // provided key/key-array is empty or malformed.
        } catch (DomainException $e) {
            throw $e;
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
        } catch (JwtSignatureInvalidException $e) {
            throw new SignatureInvalidException($e->getMessage(), 0, $e);
        } catch (JwtBeforeValidException $e) {
            throw new BeforeValidException($e->getMessage(), 0, $e);
        } catch (JwtExpiredException $e) {
            throw new ExpiredException($e->getMessage(), 0, $e);
        } catch (UnexpectedValueException $e) {
            throw $e;
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
        }
    }
}
