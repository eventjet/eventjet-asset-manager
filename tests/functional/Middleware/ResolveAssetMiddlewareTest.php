<?php

declare(strict_types=1);

namespace Eventjet\Test\Functional\AssetManager\Middleware;

use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\Middleware\ResolveAssetMiddleware;
use Eventjet\AssetManager\Resolver\PathMappingResolver;
use Eventjet\AssetManager\Service\AssetManager;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Override;
use PHPUnit\Framework\TestCase;

final class ResolveAssetMiddlewareTest extends TestCase
{
    private ResolveAssetMiddleware $middleware;

    public function testHandlerIsCalledWhenAssetIsNotFound(): void
    {
        $called = false;
        $handler = ObjectFactory::requestHandlerSpy($called);

        $this->middleware->process(
            ObjectFactory::serverRequest('GET', '/' . ObjectFactory::randomFileName() . '.jpg'),
            $handler,
        );

        self::assertTrue($called);
    }

    public function testHandlerIsNotCalledWhenAssetIsFound(): void
    {
        $fileName = ObjectFactory::randomFileName() . '.jpg';
        $called = false;
        $handler = ObjectFactory::requestHandlerSpy($called);
        ObjectFactory::tmpFile('', $fileName);

        $this->middleware->process(ObjectFactory::serverRequest('GET', '/' . $fileName), $handler);

        self::assertFalse($called);
    }

    public function testReturnsResponseOfHandlerWhenAssetIsNotFound(): void
    {
        $called = false;
        $response = new Response\TextResponse('test');
        $handler = ObjectFactory::requestHandlerSpy($called, $response);

        $result = $this->middleware->process(ObjectFactory::serverRequest('GET', '/'), $handler);

        self::assertSame('test', (string)$result->getBody());
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $paths = [ObjectFactory::pathToTmpFiles()];
        $manager = new AssetManager(
            new PathMappingResolver($paths, new FileAssetFactory()),
            new StreamFactory(),
            new ResponseFactory(),
        );
        $this->middleware = new ResolveAssetMiddleware($manager);
    }
}
