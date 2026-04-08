<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Middleware;

use Eventjet\AssetManager\Service\AssetManager;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class ResolveAssetMiddleware implements MiddlewareInterface
{
    public function __construct(private AssetManager $assetManager)
    {
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->assetManager->resolvesToAsset($request)) {
            return $handler->handle($request);
        }
        return $this->assetManager->buildAssetResponse($request);
    }
}
