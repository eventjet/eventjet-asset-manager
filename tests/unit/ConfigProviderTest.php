<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager;

use Eventjet\AssetManager\Asset\AssetFactory;
use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\ConfigProvider;
use Eventjet\AssetManager\Resolver\PathMappingResolver;
use Eventjet\AssetManager\Resolver\PathMappingResolverFactory;
use Eventjet\AssetManager\Resolver\Resolver;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testConfig(): void
    {
        $config = (new ConfigProvider())();

        self::assertArrayHasKey('dependencies', $config);
        $deps = $config['dependencies'];
        self::assertArrayHasKey('aliases', $deps);
        self::assertArrayHasKey('factories', $deps);
        self::assertArrayHasKey(AssetFactory::class, $deps['aliases']);
        self::assertArrayHasKey(Resolver::class, $deps['aliases']);
        self::assertSame(FileAssetFactory::class, $deps['aliases'][AssetFactory::class]);
        self::assertSame(PathMappingResolver::class, $deps['aliases'][Resolver::class]);
        self::assertArrayHasKey(PathMappingResolver::class, $deps['factories']);
        self::assertSame(PathMappingResolverFactory::class, $deps['factories'][PathMappingResolver::class]);
    }
}
