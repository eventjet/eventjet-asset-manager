<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Resolver;

use Eventjet\AssetManager\Asset\AssetFactoryInterface;
use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\Resolver\PathMappingResolverFactory;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use Eventjet\Test\Unit\AssetManager\TestDouble\ContainerStub;
use PHPUnit\Framework\TestCase;

use function assert;

class PathMappingResolverFactoryTest extends TestCase
{
    public function testInvokeReturnsResolverWithExpectedConfiguration(): void
    {
        $paths = ['/foo', '/bar', ObjectFactory::pathToTmpFiles()];
        $fileName = ObjectFactory::randomFileName();
        $tmpFile = ObjectFactory::tmpFile('', $fileName);
        $config = ['eventjet' => ['asset_manager' => ['paths' => $paths]]];
        $stub = new ContainerStub(['config' => $config, AssetFactoryInterface::class => new FileAssetFactory()]);

        $resolver = (new PathMappingResolverFactory())($stub);

        $asset = $resolver->resolve($fileName);
        assert($asset !== null);
        self::assertSame($tmpFile, $asset->getPath());
    }

    public function testInvokeReturnsResolverWithExpectedConfiguration2(): void
    {
        $paths = [ObjectFactory::pathToTmpFiles()];
        $fileName = ObjectFactory::randomFileName();
        $tmpFile = ObjectFactory::tmpFile('', $fileName);
        $config = ['eventjet' => ['asset_manager' => ['paths' => $paths]]];
        $stub = new ContainerStub(['config' => $config, AssetFactoryInterface::class => new FileAssetFactory()]);

        $resolver = (new PathMappingResolverFactory())($stub);

        $asset = $resolver->resolve($fileName);
        assert($asset !== null);
        self::assertSame($tmpFile, $asset->getPath());
    }
}
