<?php

declare(strict_types=1);

namespace Eventjet\AssetManager;

use Eventjet\AssetManager\Asset\AssetFactory;
use Eventjet\AssetManager\Asset\FileAssetFactory;
use Eventjet\AssetManager\Resolver\PathMappingResolver;
use Eventjet\AssetManager\Resolver\PathMappingResolverFactory;
use Eventjet\AssetManager\Resolver\Resolver;

final class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'aliases' => [
                    AssetFactory::class => FileAssetFactory::class,
                    Resolver::class => PathMappingResolver::class,
                ],
                'factories' => [
                    PathMappingResolver::class => PathMappingResolverFactory::class,
                ],
            ],
        ];
    }
}
