<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Middleware;

use Eventjet\AssetManager\Service\AssetManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ResolveAssetMiddleware implements MiddlewareInterface
{
    private AssetManager $assetManager;

    public function __construct(AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->assetManager->resolvesToAsset($request)) {
            return $handler->handle($request);
        }
        return $this->assetManager->buildAssetResponse($request);
    }
}
