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
        return [
            'service_manager' => (new ConfigProvider())->dependencyConfig(),
        ];
    }
}
