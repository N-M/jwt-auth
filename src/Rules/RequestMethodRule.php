<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Rules;

use Psr\Http\Message\ServerRequestInterface;

use function in_array;

/**
 * Rule to decide by HTTP verb whether the request should be authenticated or not.
 */
final class RequestMethodRule implements RuleInterface
{
    /**
     * @param array<string> $ignore
     */
    public function __construct(private readonly array $ignore = ['OPTIONS']) {}

    public function __invoke(ServerRequestInterface $request): bool
    {
        return !in_array($request->getMethod(), $this->ignore, true);
    }
}
