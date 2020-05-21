<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager\TestDouble;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CallbackRequestHandler implements RequestHandlerInterface
{
    private Closure $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callback)($request);
    }
}
