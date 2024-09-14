<?php

declare(strict_types=1);

use JimTools\JwtAuth\Rector\JwtAuthUpgradeRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withImportNames()
    ->withRules([JwtAuthUpgradeRector::class]);
