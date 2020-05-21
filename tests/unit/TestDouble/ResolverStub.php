<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Eventjet\AssetManager\Asset\Asset;
use Eventjet\AssetManager\Resolver\Resolver;

final class ResolverStub implements Resolver
{
    private ?Asset $asset;

    public function resolve(string $path): ?Asset
    {
        return $this->asset;
    }

    public function setResolvedAsset(?Asset $asset = null): void
    {
        $this->asset = $asset;
    }
}
