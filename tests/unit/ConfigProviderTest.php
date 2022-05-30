<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager;

use Eventjet\AssetManager\Asset\AssetFactoryInterface;
use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\ConfigProvider;
use Eventjet\AssetManager\Resolver\PathMappingResolver;
use Eventjet\AssetManager\Resolver\PathMappingResolverFactory;
use Eventjet\AssetManager\Resolver\ResolverInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testConfig(): void
    {
        /**
         * @var array{
         *     dependencies?: array{aliases?: array<string, string>, factories?: array<string, string>},
         *     eventjet: array{asset_manager: array{paths?: array<array-key, string>}},
         * } $config
         */
        $config = (new ConfigProvider())();

        self::assertArrayHasKey('dependencies', $config);
        /**
         * @var array{aliases?: array<string, string>, factories?: array<string, string>} $deps
         */
        $deps = $config['dependencies'];
        self::assertArrayHasKey('aliases', $deps);
        self::assertArrayHasKey('factories', $deps);
        self::assertArrayHasKey(AssetFactoryInterface::class, $deps['aliases']);
        self::assertArrayHasKey(ResolverInterface::class, $deps['aliases']);
        self::assertSame(FileAssetFactory::class, $deps['aliases'][AssetFactoryInterface::class]);
        self::assertSame(PathMappingResolver::class, $deps['aliases'][ResolverInterface::class]);
        self::assertArrayHasKey(PathMappingResolver::class, $deps['factories']);
        self::assertSame(PathMappingResolverFactory::class, $deps['factories'][PathMappingResolver::class]);
        self::assertArrayHasKey('paths', $config['eventjet']['asset_manager']);
        self::assertEmpty($config['eventjet']['asset_manager']['paths']);
    }
}
