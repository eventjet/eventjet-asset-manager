<?php

declare(strict_types=1);

namespace Eventjet\Test\Functional\AssetManager;

use Eventjet\AssetManager\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->module = new Module();
    }

    public function testDependencyKeyIsRenamedToServiceManager(): void
    {
        $config = $this->module->getConfig();

        self::assertArrayNotHasKey('dependencies', $config);
        self::assertArrayHasKey('service_manager', $config);
        $serviceManager = $config['service_manager'];
        self::assertArrayHasKey('aliases', $serviceManager);
        self::assertArrayHasKey('factories', $serviceManager);
    }
}
