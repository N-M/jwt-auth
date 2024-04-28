<?php

declare(strict_types=1);

namespace Tuupola\Middleware\Exceptions;

use UnexpectedValueException;

final class SignatureInvalidException extends UnexpectedValueException {}
