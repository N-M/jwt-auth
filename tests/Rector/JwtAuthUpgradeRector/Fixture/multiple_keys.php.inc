<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use Tuupola\Middleware\JwtAuthentication;

$auth = new JwtAuthentication([
  'secret' => [
    'a' => 'secret1',
    'b' => 'secret2',
  ],
  'algorithm' => ['a' => 'HS256', 'b' => 'HS256'],
  'logger' => new \stdClass,
  'rules' => [],
]);

?>
-----
<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use JimTools\JwtAuth\Middleware\JwtAuthentication;

$auth = new JwtAuthentication(new JimTools\JwtAuth\Options(), new JimTools\JwtAuth\Decoder\FirebaseDecoder(new JimTools\JwtAuth\Secret('secret1', 'HS256', 'a'), new JimTools\JwtAuth\Secret('secret2', 'HS256', 'b')));

?>
