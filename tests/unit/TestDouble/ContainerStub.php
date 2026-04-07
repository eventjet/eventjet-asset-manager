<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Override;
use Psr\Container\ContainerInterface;

use function array_key_exists;

final readonly class ContainerStub implements ContainerInterface
{
    /**
     * @param array<string, mixed> $map
     */
    public function __construct(private array $map)
    {
    }

    /**
     * @return mixed
     */
    #[Override]
    public function get(string $id)
    {
        return $this->map[$id] ?? null;
    }

    #[Override]
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->map);
    }
}
