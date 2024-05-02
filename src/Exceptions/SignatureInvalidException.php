<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Exceptions;

use UnexpectedValueException;

final class SignatureInvalidException extends UnexpectedValueException {}
