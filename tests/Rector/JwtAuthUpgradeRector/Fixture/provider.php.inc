<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Tuupola\Middleware\JwtAuthentication;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        JwtAuthentication::class => function (ContainerInterface $c) {
            $secret = $c->get(SettingsInterface::class)['secret'];

            return new JwtAuthentication([
                'secret' => $secret,
                'secure' => true,
                'relaxed' => ['localhost', '127.0.0.1'],
                'algorithm' => ['HS256'],
                'header' => 'Authorization',
                'regexp' => '/Bearer\s+(.*)$/i',
                'cookie' => 'token',
                'attribute' => 'token',
                'logger' => null,
                'before' => null,
                'after' => null,
                'error' => null,
                'garbage' => 'value',
              ]);
        },
    ]);
};

?>
-----
<?php

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector\Fixture;

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use JimTools\JwtAuth\Middleware\JwtAuthentication;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        JwtAuthentication::class => function (ContainerInterface $c) {
            $secret = $c->get(SettingsInterface::class)['secret'];

            return new JwtAuthentication(new JimTools\JwtAuth\Options(), new JimTools\JwtAuth\Decoder\FirebaseDecoder(new JimTools\JwtAuth\Secret($secret, 'HS256')));
        },
    ]);
};

?>
