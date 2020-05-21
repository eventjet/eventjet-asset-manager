<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\Asset;

use Eventjet\AssetManager\Asset\FileAsset;
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

        self::assertSame(6, (new FileAsset($filename))->getContentLength());
    }

    public function testGetMimeTypeForJs(): void
    {
        $filename = $this->createTmpFile('/** javascript */', '.js');

        self::assertSame('text/javascript', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeForJson(): void
    {
        $filename = $this->createTmpFile('{}', '.json');

        self::assertSame('text/json', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeForCss(): void
    {
        $filename = $this->createTmpFile('/** css */', '.css');

        self::assertSame('text/css', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeForPdfFiles(): void
    {
        $filename = $this->createTmpFile("%PDF-1.6", '.pdf');

        self::assertSame('application/pdf', (new FileAsset($filename))->getMimeType());
    }

    public function testGetMimeTypeForEmptyFiles(): void
    {
        $filename = $this->createTmpFile('', '.pdf');

        self::assertSame('application/x-empty', (new FileAsset($filename))->getMimeType());
    }

    private function createTmpFile(?string $content = null, ?string $ending = null): string
    {
        $ending = $ending ?? '';
        $filename = stream_get_meta_data(\Safe\tmpfile())['uri'];
        $filename = $filename . $ending;
        \Safe\file_put_contents($filename, $content ?? '');
        return $filename;
    }
}
