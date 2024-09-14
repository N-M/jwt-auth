<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use Tuupola\Middleware\JwtAuthentication\RuleInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CustomRule implements RuleInterface
{
  public function __invoke(ServerRequestInterface $request): bool
  {
    return $request->hasHeader('test');
  }
}

?>
-----
<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use JimTools\JwtAuth\Rules\RuleInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CustomRule implements RuleInterface
{
  public function __invoke(ServerRequestInterface $request): bool
  {
    return $request->hasHeader('test');
  }
}

?>
