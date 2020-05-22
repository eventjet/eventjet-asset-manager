<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Eventjet\AssetManager\Asset\AssetInterface;
use Eventjet\AssetManager\Resolver\ResolverInterface;

final class ResolverStub implements ResolverInterface
{
    private ?AssetInterface $asset;

    public function resolve(string $path): ?AssetInterface
    {
        return $this->asset;
    }

    public function setResolvedAsset(?AssetInterface $asset = null): void
    {
        $this->asset = $asset;
    }
}
