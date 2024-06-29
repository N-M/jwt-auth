<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use Tuupola\Middleware\JwtAuthentication;

$auth = new JwtAuthentication([
  'secret' => getenv('JWT_SECRET'),
  'before' => static function($req, $args) {
    return $req->withHeader('Foo', 'Bar');
  },
  'after' => static function($res, $arguments) {
    return $res->withStatus(201);
  },
]);

?>
-----
<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use JimTools\JwtAuth\Middleware\JwtAuthentication;

$auth = new JwtAuthentication(new JimTools\JwtAuth\Options(before: new class implements JimTools\JwtAuth\Handlers\BeforeHandlerInterface
{
    public function __invoke(Psr\Http\Message\ServerRequestInterface $req, array $args): Psr\Http\Message\ServerRequestInterface
    {
        return $req->withHeader('Foo', 'Bar');
    }
}, after: new class implements JimTools\JwtAuth\Handlers\AfterHandlerInterface
{
    public function __invoke(Psr\Http\Message\ResponseInterface $res, array $arguments): Psr\Http\Message\ResponseInterface
    {
        return $res->withStatus(201);
    }
}), new JimTools\JwtAuth\Decoder\FirebaseDecoder(new JimTools\JwtAuth\Secret(getenv('JWT_SECRET'), 'HS256')));

?>
