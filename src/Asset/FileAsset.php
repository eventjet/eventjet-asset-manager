<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

final class FileAsset implements AssetInterface
{
    private string $content;
    private string $fullPath;

    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
    }

    public function getPath(): string
    {
        return $this->fullPath;
    }

    public function getMimeType(): string
    {
        $mimeType = \Safe\mime_content_type($this->getPath());
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
        if ($this->content !== null) {
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
}
