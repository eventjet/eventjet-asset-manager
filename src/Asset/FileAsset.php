<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

use Narrowspark\MimeType\MimeType;

use function mb_strlen;

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
        return MimeType::guess($this->getPath());
    }

    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = \Safe\file_get_contents($this->getPath());
        }
        return $this->content;
    }

    public function getContentLength(): int
    {
        return mb_strlen($this->getContent(), '8bit');
    }
}
