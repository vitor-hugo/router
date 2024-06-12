<?php declare(strict_types=1);

namespace Torugo\Router\Traits;

trait UriTrait
{
    /**
     * Is expected that the URI starts with '/' and not finishes with '/'
     *
     * Examples:
     *   - '/user/e4a1e132'
     *   - '/v1/user/0d5d6561'
     *
     * If the URI is 'user/902e1019/', will be normalized to '/user/902e1019'
     *
     * @param string $uri
     * @return string Normalized URI
     */
    private function normalizeUri(string $uri): string
    {
        $uri = rtrim(trim($uri), "/");
        $uri = preg_replace('/\/{2,}/', "/", $uri);

        if (!str_starts_with($uri, "/")) {
            $uri = "/" . $uri;
        }

        return $uri;
    }

    /**
     * Validates if the URI has unexpected characters like GET variables.
     *
     * Example of invalid uri is '/user?id=54509acf', is expected to be something like '/user/54509acf'.
     *
     * @param string $uri
     * @return bool
     */
    private function validateUri(string $uri): bool
    {
        if (empty($uri)) {
            return true;
        }

        if (@preg_match('/^[a-zA-Z0-9\/\-\.]*$/', $uri) == false) {
            return false;
        }

        return true;
    }

    /**
     * Validate if the route URI has unexpected characters
     * @param string $route Registered route from a controller
     * @return bool
     */
    private function validateRouteUri(string $route): bool
    {
        if (empty($route)) {
            return true;
        }

        if (@preg_match('/^[a-zA-Z0-9\/\-\.\{\}]*$/', $route) == false) {
            return false;
        }

        return true;
    }
}
