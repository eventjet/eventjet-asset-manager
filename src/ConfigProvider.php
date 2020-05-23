<?php

declare(strict_types=1);

namespace Eventjet\AssetManager;

use Eventjet\AssetManager\Asset\AssetFactoryInterface;
use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\Resolver\PathMappingResolver;
use Eventjet\AssetManager\Resolver\PathMappingResolverFactory;
use Eventjet\AssetManager\Resolver\ResolverInterface;

final class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->dependencyConfig(),
            'eventjet' => [
                'asset_manager' => [
                    'paths' => [],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function dependencyConfig(): array
    {
        return [
            'aliases' => [
                AssetFactoryInterface::class => FileAssetFactory::class,
                ResolverInterface::class => PathMappingResolver::class,
            ],
            'factories' => [
                PathMappingResolver::class => PathMappingResolverFactory::class,
            ],
        ];
    }
}
