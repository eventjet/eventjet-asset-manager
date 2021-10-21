<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Psr\Container\ContainerInterface;

use function array_key_exists;

final class ContainerStub implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $map;

    /**
     * @param array<string, mixed> $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->map[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->map);
    }

    /**
     * @param array<string, mixed> $map
     */
    public function setMap(array $map): void
    {
        $this->map = $map;
    }
}
