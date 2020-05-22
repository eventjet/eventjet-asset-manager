<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Service;

use DateTimeZone;
use Eventjet\AssetManager\Resolver\ResolverInterface;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;

use function gmdate;
use function is_string;

use const DATE_RFC7231;

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

    public function buildAssetResponse(ServerRequestInterface $request): ResponseInterface
    {
        $asset = $this->resolver->resolve($request->getUri()->getPath());
        if ($asset === null) {
            throw new RuntimeException(
                'Asset could not be resolved. Use "resolvesToAsset" before "buildAssetResponse".'
            );
        }
        $lastModified = \Safe\filemtime($asset->getPath());
        $etagFile = \Safe\md5_file($asset->getPath());

        $serverParams = $request->getServerParams();
        $ifModifiedSince = $serverParams['HTTP_IF_MODIFIED_SINCE'] ?? null;
        $etagHeader = $serverParams['HTTP_IF_NONE_MATCH'] ?? null;

        if (is_string($ifModifiedSince)) {
            try {
                $ifModifiedSince = DateTimeImmutable::createFromFormat(
                    DATE_RFC7231,
                    $ifModifiedSince,
                    new DateTimeZone('UTC')
                )->getTimestamp();
            } catch (DatetimeException $exception) {
                $ifModifiedSince = null;
            }
        }

        $response = (new Response())
            ->withAddedHeader('Last-Modified', gmdate(DATE_RFC7231, $lastModified))
            ->withAddedHeader('Etag', $etagFile)
            ->withAddedHeader('Cache-Control', 'public');

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
