<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

use Override;

final class FileAssetFactory implements AssetFactoryInterface
{
    #[Override]
    public function create(string $path): AssetInterface
    {
        return new FileAsset($path);
    }
}
