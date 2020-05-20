<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Resolver;

use Eventjet\AssetManager\Asset\AssetFactoryInterface;
use Eventjet\AssetManager\Asset\AssetInterface;

final class PathMappingResolver implements ResolverInterface
{
    /** @var array<string, string> */
    private array $pathMapping;
    private AssetFactoryInterface $factory;

    /**
     * @param array<string, string> $pathMapping
     */
    public function __construct(array $pathMapping, AssetFactoryInterface $factory)
    {
        $this->pathMapping = $pathMapping;
        $this->factory = $factory;
    }

    public function resolve(string $path): ?AssetInterface
    {
        if ($path === '/') {
            return null;
        }
        foreach ($this->pathMapping as $current) {
            $fullPath = $current . $path;
            if (!file_exists($fullPath)) {
                continue;
            }
            return $this->factory->create($fullPath);
        }
        return null;
    }
}
