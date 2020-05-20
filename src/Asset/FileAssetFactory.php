<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

final class FileAssetFactory implements AssetFactory
{
    public function create(string $path): Asset
    {
        return new FileAsset($path);
    }
}
