<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Resolver;

use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\Resolver\PathMappingResolver;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use PHPUnit\Framework\TestCase;

use function assert;

class PathMappingResolverTest extends TestCase
{
    public function testResolveReturnsNullWhenResolvingRoot(): void
    {
        self::assertNull((new PathMappingResolver([], new FileAssetFactory()))->resolve('/'));
    }

    public function testResolveReturnsNullForAssetThatDoesNotExist(): void
    {
        self::assertNull((new PathMappingResolver([], new FileAssetFactory()))->resolve('test'));
    }

    public function testResolveReturnsAssetThatExists(): void
    {
        $paths = [ObjectFactory::pathToTmpFiles()];
        $fileName = ObjectFactory::randomFileName();
        $tmpFile = ObjectFactory::tmpFile('', $fileName);

        $asset = (new PathMappingResolver($paths, new FileAssetFactory()))->resolve($fileName);

        assert($asset !== null);
        self::assertSame($tmpFile, $asset->getPath());
    }

    public function testResolveIteratesOverAllPaths(): void
    {
        $paths = ['/foo', '/bar', ObjectFactory::pathToTmpFiles()];
        $fileName = ObjectFactory::randomFileName();
        $tmpFile = ObjectFactory::tmpFile('', $fileName);

        $asset = (new PathMappingResolver($paths, new FileAssetFactory()))->resolve($fileName);

        assert($asset !== null);
        self::assertSame($tmpFile, $asset->getPath());
    }
}
