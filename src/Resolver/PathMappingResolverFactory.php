<?php

declare(strict_types=1);

namespace Eventjet\AssetManager\Resolver;

use Eventjet\AssetManager\Asset\AssetFactoryInterface;
use Psr\Container\ContainerInterface;

final class PathMappingResolverFactory
{
    public function __invoke(ContainerInterface $container): PathMappingResolver
    {
        return new PathMappingResolver(
            $this->paths($container),
            $container->get(AssetFactoryInterface::class)
        );
    }

    /**
     * @return array<array-key, string>
     */
    private function paths(ContainerInterface $container): array
    {
        /** @var array{eventjet?: array{asset_manager?: array{paths?: array<array-key, string>}}} $config */
        $config = $container->get('config');
        return $config['eventjet']['asset_manager']['paths'] ?? [];
    }
}
