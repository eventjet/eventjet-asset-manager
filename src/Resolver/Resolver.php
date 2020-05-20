<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Resolver;

use Eventjet\AssetManager\Asset\Asset;

interface Resolver
{
    public function resolve(string $path): ?Asset;
}
