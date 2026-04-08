<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

use Override;
use SplFileInfo;

use function file_get_contents;
use function filesize;
use function strtolower;

final class FileAsset implements AssetInterface
{
    private string|null $content;
    private string $fullPath;

    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
        $this->content = null;
    }

    #[Override]
    public function getPath(): string
    {
        return $this->fullPath;
    }

    #[Override]
    public function getMimeType(): string
    {
        return $this->findMimeType($this->getExtension()) ?? 'application/octet-stream';
    }

    #[Override]
    public function getContent(): string
    {
        if ($this->content === null) {
            $fileContents = file_get_contents($this->getPath());

            if ($fileContents !== false) {
                $this->content = $fileContents;
            }
        }
        return $this->content ?? '';
    }

    #[Override]
    public function getContentLength(): string
    {
        $size = filesize($this->fullPath);
        return $size === false ? '0' : (string)$size;
    }

    private function getExtension(): string
    {
        return (new SplFileInfo($this->getPath()))->getExtension();
    }

    private function findMimeType(string $extension): string|null
    {
        $extension = strtolower($extension);
        return MimeTypesList::MIMES[$extension][0] ?? null;
    }
}
