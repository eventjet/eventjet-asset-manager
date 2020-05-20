<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Asset;

interface AssetFactory
{
    public function create(string $path): Asset;
}
