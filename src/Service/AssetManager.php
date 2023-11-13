<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Service;

use DateTimeImmutable;
use DateTimeZone;
use Eventjet\AssetManager\Resolver\ResolverInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;

use function filemtime;
use function gmdate;
use function is_string;
use function md5_file;
use function time;

use const DATE_RFC7231;

final class AssetManager
{
    private ResolverInterface $resolver;
    private StreamFactoryInterface $streamFactory;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        ResolverInterface $resolver,
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->resolver = $resolver;
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;
    }

    public function resolvesToAsset(RequestInterface $request): bool
    {
        return $this->resolver->resolve($request->getUri()->getPath()) !== null;
    }

    public function buildAssetResponse(ServerRequestInterface $request): ResponseInterface
    {
        $asset = $this->resolver->resolve($request->getUri()->getPath());
        if ($asset === null) {
            throw new RuntimeException(
                'Asset could not be resolved. Use "resolvesToAsset" before "buildAssetResponse".'
            );
        }
        $lastModified = filemtime($asset->getPath());
        if ($lastModified === false) {
            $lastModified = time();
        }

        $etagFile = md5_file($asset->getPath());
        if ($etagFile === false) {
            $etagFile = null;
        }

        $serverParams = $request->getServerParams();
        /** @var string|null $ifModifiedSince */
        $ifModifiedSince = $serverParams['HTTP_IF_MODIFIED_SINCE'] ?? null;
        /** @var string|null $etagHeader */
        $etagHeader = $serverParams['HTTP_IF_NONE_MATCH'] ?? null;

        if (is_string($ifModifiedSince)) {
            $modifiedDate = DateTimeImmutable::createFromFormat(
                DATE_RFC7231,
                $ifModifiedSince,
                new DateTimeZone('UTC')
            );

            if ($modifiedDate instanceof DateTimeImmutable) {
                $ifModifiedSince = $modifiedDate->getTimestamp();
            } else {
                $ifModifiedSince = null;
            }
        }

        $response = $this->responseFactory->createResponse()
            ->withAddedHeader('Last-Modified', gmdate(DATE_RFC7231, $lastModified))
            ->withAddedHeader('Cache-Control', 'public');
        if ($etagFile !== null) {
            $response = $response->withAddedHeader('Etag', $etagFile);
        }
        if ($etagHeader === $etagFile || $ifModifiedSince >= $lastModified) {
            return $response
                ->withStatus(StatusCodeInterface::STATUS_NOT_MODIFIED, 'Not Modified');
        }

        return $response
            ->withStatus(StatusCodeInterface::STATUS_OK)
            ->withAddedHeader('Content-Transfer-Encoding', 'binary')
            ->withAddedHeader('Content-Type', $asset->getMimeType())
            ->withAddedHeader('Content-Length', $asset->getContentLength())
            ->withBody($this->streamFactory->createStreamFromFile($asset->getPath()));
    }
}
