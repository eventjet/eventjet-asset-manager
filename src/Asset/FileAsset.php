<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

use SplFileInfo;

use function strlen;
use function strtolower;

final class FileAsset implements AssetInterface
{
    private ?string $content;
    private string $fullPath;

    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
        $this->content = null;
    }

    public function getPath(): string
    {
        return $this->fullPath;
    }

    public function getMimeType(): string
    {
        return $this->findMimeType($this->getExtension()) ?? 'application/octet-stream';
    }

    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = \Safe\file_get_contents($this->getPath());
        }
        return $this->content;
    }

    public function getContentLength(): string
    {
        return (string)strlen($this->getContent());
    }

    private function getExtension(): string
    {
        return (new SplFileInfo($this->getPath()))->getExtension();
    }

    private function findMimeType(string $extension): ?string
    {
        $extension = strtolower($extension);
        return MimeTypesList::MIMES[$extension][0] ?? null;
    }
}
