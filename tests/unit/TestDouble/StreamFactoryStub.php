<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use Laminas\Diactoros\Stream;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final readonly class StreamFactoryStub implements StreamFactoryInterface
{
    #[Override]
    public function createStream(string $content = ''): StreamInterface
    {
        return new Stream($content);
    }

    #[Override]
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $filename = ObjectFactory::tmpFile();
        return new Stream($filename, $mode);
    }

    #[Override]
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
