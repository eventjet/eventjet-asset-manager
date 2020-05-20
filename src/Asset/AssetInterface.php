<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

interface AssetInterface
{
    public function getPath(): string;
    public function getMimeType(): string;
    public function getContentLength(): int;
    public function getContent(): string;
}
