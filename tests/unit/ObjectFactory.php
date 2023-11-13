<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\AssetManager;

use Eventjet\Test\Unit\AssetManager\TestDouble\CallbackRequestHandler;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplFileInfo;

use function assert;
use function basename;
use function bin2hex;
use function file_put_contents;
use function random_bytes;
use function str_replace;
use function stream_get_meta_data;
use function tmpfile;

use const DIRECTORY_SEPARATOR;

final class ObjectFactory
{
    public static function tmpFileName(): string
    {
        $file = tmpfile();
        assert($file !== false);
        return stream_get_meta_data($file)['uri'];
    }

    public static function pathToTmpFiles(): string
    {
        return (new SplFileInfo(self::tmpFileName()))->getPath() . DIRECTORY_SEPARATOR;
    }

    public static function tmpFile(?string $content = null, ?string $filename = null): string
    {
        $tmpFile = self::tmpFileName();
        if ($filename !== null) {
            $tmpFile = str_replace(basename($tmpFile), $filename, $tmpFile);
        }
        file_put_contents($tmpFile, $content ?? '');
        return $tmpFile;
    }

    /**
     * @param array<string, mixed> $serverParams
     */
    public static function serverRequest(
        ?string $method = null,
        ?string $uri = null,
        ?array $serverParams = null
    ): ServerRequestInterface {
        return (new ServerRequestFactory())->createServerRequest($method ?? 'GET', $uri ?? '/', $serverParams ?? []);
    }

    public static function response(?int $code = null, ?string $reasonPhrase = null): ResponseInterface
    {
        return (new ResponseFactory())->createResponse($code ?? 200, $reasonPhrase ?? '');
    }

    public static function randomFileName(): string
    {
        return bin2hex(random_bytes(5));
    }

    public static function requestHandlerSpy(bool &$called): RequestHandlerInterface
    {
        return new CallbackRequestHandler(
            function () use (&$called): ResponseInterface {
                $called = true;
                return self::response();
            }
        );
    }
}
