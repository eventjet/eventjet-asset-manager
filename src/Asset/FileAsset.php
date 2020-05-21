<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

use function function_exists;
use function mb_strlen;
use function pathinfo;
use function strlen;

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
        $mimeType = $this->mimeType($this->getPath());
        if ($mimeType !== 'text/plain') {
            return $mimeType;
        }
        ['extension' => $extension] = pathinfo($this->getPath());
        if ($extension === 'css') {
            return 'text/css';
        }
        if ($extension === 'js') {
            return 'text/javascript';
        }
        return $mimeType;
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
        if (!function_exists('mb_strlen')) {
            return strlen($this->getContent());
        }
        return mb_strlen($this->getContent(), '8bit');
    }

    private function mimeType(string $filename): string
    {
        return \Safe\mime_content_type($filename);
        // todo remove when we solved mime type issues
        //$result = new \finfo();
        //return $result->file($filename, FILEINFO_MIME_TYPE);
    }
}
