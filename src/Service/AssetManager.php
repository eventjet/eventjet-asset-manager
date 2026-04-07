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
use function sprintf;
use function time;

use const DATE_RFC7231;

final readonly class AssetManager
{
    public function __construct(
        private ResolverInterface $resolver,
        private StreamFactoryInterface $streamFactory,
        private ResponseFactoryInterface $responseFactory,
        private string $maxAge = '86400',
    ) {
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
                'Asset could not be resolved. Use "resolvesToAsset" before "buildAssetResponse".',
            );
        }
        $lastModified = filemtime($asset->getPath());
        if ($lastModified === false) {
            $lastModified = time();
        }

        $contentLength = $asset->getContentLength();
        if ($contentLength === '0') {
            $etagFile = null;
        } else {
            $etagFile = sprintf(
                '%x-%x',
                $lastModified,
                $contentLength,
            );
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
                new DateTimeZone('UTC'),
            );

            if ($modifiedDate instanceof DateTimeImmutable) {
                $ifModifiedSince = $modifiedDate->getTimestamp();
            } else {
                $ifModifiedSince = null;
            }
        }

        $response = $this->responseFactory->createResponse()
            ->withAddedHeader('Last-Modified', gmdate(DATE_RFC7231, $lastModified))
            ->withAddedHeader('Cache-Control', sprintf('public, max-age=%s', $this->maxAge));
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
            ->withAddedHeader('Content-Length', $contentLength)
            ->withBody($this->streamFactory->createStreamFromFile($asset->getPath()));
    }
}
