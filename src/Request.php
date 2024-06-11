<?php declare(strict_types=1);

namespace Torugo\Router;

use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidRouteException;

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
        $data = [];

        $data = @json_decode(file_get_contents("php://input"), true);

        if (empty($data) || $data == false) {
            $data = [];
            parse_str(file_get_contents('php://input'), $data);
        }

        return $data ?? [];
    }

    public static function getUri(): string
    {
        $uri = $_SERVER["REQUEST_URI"] ?? "";
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

    /**
     * Redirects the current request to a new URL
     * @param string $url URL to be redirected
     * @param int $statusCode HTTP status code. (default is 301)
     * @return never
     */
    public static function redirect(string $url, int $statusCode = 301): never
    {
        if (!preg_match('/^[a-zA-Z0-9\-\_\.\~\!\*\'\(\)\;\:\@\&\=\+\$\,\/\?\%\#\[\]]*$/', $url)) {
            throw new InvalidRouteException("Invalid redirection URL.", 1);
        }

        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * Returns an array ['HeaderName' => 'HeaderValue'] list with all request headers
     * @return array
     */
    public static function getHeaders(): array
    {
        return getallheaders();
    }

    /**
     * Returns all values as an array of strings, if not found returns an empty array.
     * @param string $headerName Name of the required header
     * @return array
     */
    public static function getHeader(string $headerName): array
    {
        $result = [];

        $headerName = mb_strtoupper($headerName);
        foreach (getallheaders() as $name => $value) {
            if ($headerName == mb_strtoupper($name)) {
                $result[] = $value;
            }
        }

        return $result;
    }
}
