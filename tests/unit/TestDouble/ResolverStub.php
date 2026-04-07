<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Eventjet\AssetManager\Asset\AssetInterface;
use Eventjet\AssetManager\Resolver\ResolverInterface;
use Override;

final class ResolverStub implements ResolverInterface
{
    private AssetInterface|null $asset = null;

    #[Override]
    public function resolve(string $path): AssetInterface|null
    {
        return $this->asset;
    }

    public function setResolvedAsset(AssetInterface|null $asset = null): void
    {
        $this->asset = $asset;
    }
}
