<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Service;

use Eventjet\AssetManager\Resolver\ResolverInterface;
use Laminas\Diactoros\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;

final class AssetManager
{
    private ResolverInterface $resolver;
    private StreamFactoryInterface $streamFactory;

    public function __construct(ResolverInterface $resolver, StreamFactoryInterface $streamFactory)
    {
        $this->resolver = $resolver;
        $this->streamFactory = $streamFactory;
    }

    public function resolvesToAsset(RequestInterface $request): bool
    {
        return $this->resolver->resolve($request->getUri()->getPath()) !== null;
    }

    public function buildAssetResponse(RequestInterface $request): ResponseInterface
    {
        $asset = $this->resolver->resolve($request->getUri()->getPath());
        if ($asset === null) {
            throw new RuntimeException(
                'Asset could not be resolved. Use "resolvesToAsset" before "buildAssetResponse".'
            );
        }
        return (new Response())
            ->withStatus(200)
            ->withAddedHeader('Content-Transfer-Encoding', 'binary')
            ->withAddedHeader('Content-Type', $asset->getMimeType())
            ->withAddedHeader('Content-Length', $asset->getContentLength())
            ->withBody($this->streamFactory->createStreamFromFile($asset->getPath()));
    }
}
