<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

interface Asset
{
    public function getPath(): string;

    public function getMimeType(): string;

    public function getContentLength(): string;

    public function getContent(): string;
}
