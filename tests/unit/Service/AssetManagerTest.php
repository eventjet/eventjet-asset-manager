<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Service;

use DateTimeZone;
use Eventjet\AssetManager\Asset\FileAsset;
use Eventjet\AssetManager\Service\AssetManager;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use Eventjet\Test\Unit\AssetManager\TestDouble\ResolverStub;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Safe\DateTimeImmutable;

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

        $expectedLastModify = DateTimeImmutable::createFromFormat(
            'U',
            \Safe\filemtime($asset->getPath()),
            new DateTimeZone('UTC')
        )->format(DATE_RFC7231);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('binary', $response->getHeaderLine('Content-Transfer-Encoding'));
        self::assertSame('application/javascript', $response->getHeaderLine('Content-Type'));
        self::assertSame('9', $response->getHeaderLine('Content-Length'));
        self::assertSame($expectedLastModify, $response->getHeaderLine('Last-Modified'));
        self::assertSame(\Safe\md5_file($asset->getPath()), $response->getHeaderLine('Etag'));
        self::assertSame('public', $response->getHeaderLine('Cache-Control'));
        self::assertSame('/** js */', $response->getBody()->getContents());
    }

    public function testReturnsNotModifiedIfEtagMatches(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $request = ObjectFactory::serverRequest(
            null,
            null,
            ['HTTP_IF_NONE_MATCH' => \Safe\md5_file($asset->getPath())]
        );

        $response = $this->manager->buildAssetResponse($request);

        self::assertSame(StatusCodeInterface::STATUS_NOT_MODIFIED, $response->getStatusCode());
    }

    public function testReturnsNotModifiedIfNotModifiedSinceMatches(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $filemtime = \Safe\filemtime($asset->getPath());
        $wantedLastModify = DateTimeImmutable::createFromFormat('U', $filemtime, new DateTimeZone('UTC'))
            ->format(DATE_RFC7231);
        $request = ObjectFactory::serverRequest(
            null,
            null,
            ['HTTP_IF_MODIFIED_SINCE' => $wantedLastModify]
        );

        $response = $this->manager->buildAssetResponse($request);

        self::assertSame(StatusCodeInterface::STATUS_NOT_MODIFIED, $response->getStatusCode());
    }

    public function testInvalidIfNotModifiedSinceHeaderIsIgnored(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $request = ObjectFactory::serverRequest(
            null,
            null,
            ['HTTP_IF_MODIFIED_SINCE' => 'foo']
        );

        $response = $this->manager->buildAssetResponse($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ResolverStub();
        $this->manager = new AssetManager($this->resolver, new StreamFactory());
    }
}
