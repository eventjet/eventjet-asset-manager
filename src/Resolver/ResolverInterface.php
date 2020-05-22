<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Resolver;

use Eventjet\AssetManager\Asset\AssetInterface;

interface ResolverInterface
{
    public function resolve(string $path): ?AssetInterface;
}
