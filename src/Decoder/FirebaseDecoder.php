<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Decoder;

use DomainException as BaseDomainException;
use Firebase\JWT\BeforeValidException as JwtBeforeValidException;
use Firebase\JWT\ExpiredException as JwtExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException as JwtSignatureInvalidException;
use InvalidArgumentException as BaseInvalidArgumentException;
use JimTools\JwtAuth\Exceptions\BeforeValidException;
use JimTools\JwtAuth\Exceptions\DomainException;
use JimTools\JwtAuth\Exceptions\ExpiredException;
use JimTools\JwtAuth\Exceptions\InvalidArgumentException;
use JimTools\JwtAuth\Exceptions\SignatureInvalidException;
use JimTools\JwtAuth\Exceptions\UnexpectedValueException;
use JimTools\JwtAuth\Secret;
use UnexpectedValueException as BaseUnexpectedValueException;

use function count;

final class FirebaseDecoder implements DecoderInterface
{
    /**
     * @var array<string, Key>|Key[]
     */
    private array $keys = [];

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
    }

    public function decode(string $jwt): array
    {
        try {
            $keys = $this->keys;
            if (count($this->keys) === 1) {
                $keys = current($this->keys);
            }

            return (array) JWT::decode($jwt, $keys);
        } catch (BaseInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), 0, $e);
        } catch (BaseDomainException $e) {
            throw new DomainException($e->getMessage(), 0, $e);
        } catch (JwtSignatureInvalidException $e) {
            throw new SignatureInvalidException($e->getMessage(), 0, $e);
        } catch (JwtBeforeValidException $e) {
            throw new BeforeValidException($e->getMessage(), 0, $e);
        } catch (JwtExpiredException $e) {
            throw new ExpiredException($e->getMessage(), 0, $e);
        } catch (BaseUnexpectedValueException $e) {
            throw new UnexpectedValueException($e->getMessage(), 0, $e);
        }
    }
}
