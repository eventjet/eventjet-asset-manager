<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

use Narrowspark\MimeType\MimeTypeExtensionGuesser;
use SplFileInfo;

final class FileAsset implements Asset
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
        return MimeTypeExtensionGuesser::guess($this->getExtension()) ?? 'application/octet-stream';
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
}
