<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Asset;

use Eventjet\AssetManager\Asset\FileAsset;
use Eventjet\Test\Unit\AssetManager\ObjectFactory;
use PHPUnit\Framework\TestCase;

class FileAssetTest extends TestCase
{
    public function testGetPath(): void
    {
        self::assertSame('foo', (new FileAsset('foo'))->getPath());
    }

    public function testGetContent(): void
    {
        $filename = $this->createTmpFile('foobar');

        self::assertSame('foobar', (new FileAsset($filename))->getContent());
    }

    public function testGetContentLength(): void
    {
        $filename = $this->createTmpFile('foobar');

        self::assertSame('6', (new FileAsset($filename))->getContentLength());
    }

    public function testGetContentLengthForMbString(): void
    {
        self::markTestIncomplete('We need an example for an mb_ string.');
        // Then replace "strlen($this->getContent())" with "mb_strlen($this->getContent(), '8bit')"
    }

    public function testGetMimeTypeForJs(): void
    {
        $filename = $this->createTmpFile('/** javascript */', '.js');

        self::assertSame('application/javascript', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeForJson(): void
    {
        $filename = $this->createTmpFile('/** json */', '.json');

        self::assertSame('application/json', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeForCss(): void
    {
        $filename = $this->createTmpFile('/** css */', '.css');

        self::assertSame('text/css', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeFallsBackToOctetStreamMimeType(): void
    {
        $filename = $this->createTmpFile('', '');

        self::assertSame('application/octet-stream', (new FileAsset($filename))->getMimeType());
    }

    private function createTmpFile(?string $content = null, ?string $ending = null): string
    {
        $filename = ObjectFactory::randomFileName();
        $filename = $filename . $ending;
        return ObjectFactory::tmpFile($content, $filename);
    }
}
