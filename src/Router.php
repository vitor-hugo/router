<?php declare(strict_types=1);

namespace Torugo\Router;

use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Traits\UriTrait;

class Router
{
    use UriTrait;

    /**
     * List of registered controllers' routes
     * @var array
     */
    private array $routes = [];

    /**
     * Routes prefix
     * @var string
     */
    private string $prefix = "";

    /**
     * Returns the list of registered controllers' routes
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns the defined route prefix
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Defines a prefix to all routes, like '/v1' or '/beta'
     * @param string $prefix
     * @throws \Torugo\Router\Exceptions\InvalidRouteException
     * @return void
     */
    public function setPrefix(string $prefix): void
    {
        $prefix = $this->normalizeUri($prefix);

        if ($this->validateUri($prefix) == false) {
            throw new InvalidRouteException("The route prefix '$prefix' is invalid.", 1);
        }

        $this->prefix = $prefix;
    }
}
