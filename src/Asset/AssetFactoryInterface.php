<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

interface AssetFactoryInterface
{
    public function create(string $path): AssetInterface;
}
