<?php declare(strict_types=1);

namespace Torugo\Router;

use Torugo\Router\Enums\RequestMethod;

/**
 * Access to Request's method, uri and data
 */
class Request
{
    public static function getMethod(): RequestMethod
    {
        return RequestMethod::tryFromRequest();
    }

    public static function getData(): array
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true, JSON_THROW_ON_ERROR);
        } catch (\Throwable $th) {
            parse_str(file_get_contents("php://input"), $data);
        }

        return $data;
    }

    public static function getUri(): string
    {
        $uri = $_SERVER["REQUEST_URI"];
        $uri = self::normalizeUri($uri);
        return $uri;
    }

    private static function normalizeUri(string $uri): string
    {
        $uri = rtrim(trim($uri), "/");

        if (!str_starts_with($uri, "/")) {
            $uri = "/" . $uri;
        }

        return $uri;
    }
}
