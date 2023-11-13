<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Service;

use Eventjet\AssetManager\Asset\FileAsset;
use Eventjet\AssetManager\Service\AssetManager;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use Eventjet\Test\Unit\AssetManager\TestDouble\ResolverStub;
use Eventjet\Test\Unit\AssetManager\TestDouble\StreamFactoryStub;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function assert;
use function filemtime;
use function gmdate;
use function md5_file;
use function time;

use const DATE_RFC7231;

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

        $lastModifiedTimestamp = filemtime($asset->getPath());
        self::assertNotFalse($lastModifiedTimestamp);
        $expectedLastModify = gmdate(DATE_RFC7231, $lastModifiedTimestamp);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('binary', $response->getHeaderLine('Content-Transfer-Encoding'));
        self::assertSame('application/javascript', $response->getHeaderLine('Content-Type'));
        self::assertSame('9', $response->getHeaderLine('Content-Length'));
        self::assertSame($expectedLastModify, $response->getHeaderLine('Last-Modified'));
        self::assertSame(md5_file($asset->getPath()), $response->getHeaderLine('Etag'));
        self::assertSame('public', $response->getHeaderLine('Cache-Control'));
        self::assertSame('/** js */', $response->getBody()->getContents());
    }

    public function testLastModifiedIsNowIfFileMTimeCouldNotBeRead(): void
    {
        $manager = new AssetManager($this->resolver, new StreamFactoryStub(), new ResponseFactory());
        $asset = new FileAsset('non-existing');
        $this->resolver->setResolvedAsset($asset);

        $response = $manager->buildAssetResponse(ObjectFactory::serverRequest());

        self::assertSame(gmdate(DATE_RFC7231, time()), $response->getHeaderLine('Last-Modified'));
    }

    public function testResponseHasNoEtagIfHashCouldNotBeCreated(): void
    {
        $manager = new AssetManager($this->resolver, new StreamFactoryStub(), new ResponseFactory());
        $asset = new FileAsset('non-existing');
        $this->resolver->setResolvedAsset($asset);

        $response = $manager->buildAssetResponse(ObjectFactory::serverRequest());

        self::assertFalse($response->hasHeader('Etag'));
    }

    public function testReturnsNotModifiedIfEtagMatches(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $request = ObjectFactory::serverRequest(
            null,
            null,
            ['HTTP_IF_NONE_MATCH' => md5_file($asset->getPath())],
        );

        $response = $this->manager->buildAssetResponse($request);

        self::assertSame(StatusCodeInterface::STATUS_NOT_MODIFIED, $response->getStatusCode());
    }

    public function testReturnsNotModifiedIfModifiedSinceMatches(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $filemtime = filemtime($asset->getPath());
        assert($filemtime !== false);
        $wantedLastModify = gmdate(DATE_RFC7231, $filemtime);
        $request = ObjectFactory::serverRequest(
            null,
            null,
            ['HTTP_IF_MODIFIED_SINCE' => $wantedLastModify]
        );

        $response = $this->manager->buildAssetResponse($request);

        self::assertSame(StatusCodeInterface::STATUS_NOT_MODIFIED, $response->getStatusCode());
    }

    public function testReturnsCompleteAssetIfModifiedSinceIsOlderThanFile(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $filemtime = filemtime($asset->getPath());
        assert($filemtime !== false);
        $wantedLastModify = gmdate(DATE_RFC7231, $filemtime - 86400);
        $request = ObjectFactory::serverRequest(
            null,
            null,
            ['HTTP_IF_MODIFIED_SINCE' => $wantedLastModify]
        );

        $response = $this->manager->buildAssetResponse($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testReturnsNotModifiedIfModifiedSinceIsNewerThanFile(): void
    {
        $asset = new FileAsset(ObjectFactory::tmpFile('/** js */', 'test.js'));
        $this->resolver->setResolvedAsset($asset);
        $filemtime = filemtime($asset->getPath());
        assert($filemtime !== false);
        $wantedLastModify = gmdate(DATE_RFC7231, $filemtime + 86400);
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
        $this->manager = new AssetManager($this->resolver, new StreamFactory(), new ResponseFactory());
    }
}
