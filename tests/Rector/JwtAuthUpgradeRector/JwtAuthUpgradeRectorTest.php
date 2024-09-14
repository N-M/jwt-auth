<?php

declare(strict_types=1);

namespace JimTools\JwtAuth\Test\Rector\JwtAuthUpgradeRector;

use Iterator;
use JimTools\JwtAuth\Rector\JwtAuthUpgradeRector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @internal
 */
#[CoversClass(JwtAuthUpgradeRector::class)]
final class JwtAuthUpgradeRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/config.php';
    }
}
