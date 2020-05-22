<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

final class FileAssetFactory implements AssetFactoryInterface
{
    public function create(string $path): AssetInterface
    {
        return new FileAsset($path);
    }
}
