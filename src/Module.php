<?php

declare(strict_types=1);

namespace Eventjet\AssetManager;

final class Module
{
    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        $config = (new ConfigProvider())();
        $config['service_manager'] = $config['dependencies'] ?? [];
        unset($config['dependencies']);
        return $config;
    }
}
