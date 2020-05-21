<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Service;

use Eventjet\AssetManager\Asset\FileAsset;
use Eventjet\AssetManager\Service\AssetManager;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use Eventjet\Test\Unit\AssetManager\TestDouble\ResolverStub;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AssetManagerTest extends TestCase
{
    private ResolverStub $resolver;
    private AssetManager $manager;

    public function testBuildAssetResponseThrowsExceptionOnUnresolvableAsset(): void
    {
        $this->resolver->setResolvedAsset(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Asset could not be resolved. Use "resolvesToAsset" before "buildAssetResponse".'
        );

        $this->manager->buildAssetResponse(ObjectFactory::serverRequest());
    }

    public function testBuildAssetResponseReturnsSuccessResponse(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);

        $response = $this->manager->buildAssetResponse(ObjectFactory::serverRequest());

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('binary', $response->getHeaders()['Content-Transfer-Encoding'][0]);
        self::assertSame('application/javascript', $response->getHeaders()['Content-Type'][0]);
        self::assertSame('9', $response->getHeaders()['Content-Length'][0]);
        self::assertSame('/** js */', $response->getBody()->getContents());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ResolverStub();
        $this->manager = new AssetManager($this->resolver, new StreamFactory());
    }
}
