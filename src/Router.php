<?php declare(strict_types=1);

namespace Torugo\Router;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Route;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidControllerExeception;
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

    /**
     * Register an array of Modules Controllers
     * @param array $controllers Array of Modules Controllers classes namespaces
     * @return void
     */
    public function registerMany(array $controllers): void
    {
        foreach ($controllers as $controller) {
            $this->register($controller);
        }
    }

    /**
     * Adds a controller to the route list
     * @param string $controller Controller class namespace
     * @return void
     */
    public function register(string $controller)
    {
        $reflection = $this->instantiateControllerReflectionClass($controller);
        $methods = $reflection->getMethods();
        $controllerAttribute = $this->getControllerAttributeInstance($reflection);

        foreach ($methods as $method) {
            $routeAttributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($routeAttributes as $routeAttribute) {
                $route = $routeAttribute->newInstance();
                $this->addRoute($controllerAttribute, $route, $method);
            }
        }
    }

    /**
     * Returns a ReflectionClass instance of the Controller class. (Not Controller Attribute).
     * @param string $controller Module Controller class
     * @throws \Torugo\Router\Exceptions\InvalidControllerExeception
     * @return \ReflectionClass
     */
    private function instantiateControllerReflectionClass(string $controller): ReflectionClass
    {
        try {
            $reflection = new ReflectionClass($controller);
        } catch (\Throwable $_) {
            throw new InvalidControllerExeception("The controller '{$controller}' not found.", 1);
        }

        return $reflection;
    }

    /**
     * Returns a instance of class' Controller Attribute, if not present throws an exception.
     * @param \ReflectionClass $reflection
     * @throws \Torugo\Router\Exceptions\InvalidControllerExeception
     * @return \Torugo\Router\Attributes\Request\Controller
     */
    private function getControllerAttributeInstance(ReflectionClass $reflection): Controller
    {
        $attr = $reflection->getAttributes(Controller::class, ReflectionAttribute::IS_INSTANCEOF);

        if (count($attr) != 1) {
            throw new InvalidControllerExeception("The controller '{$reflection->getName()}' is invalid, controllers must use #[Controller()] attribute.", 2);
        }

        return $attr[0]->newInstance();
    }

    /**
     * Summary of addRoute
     * @param \Torugo\Router\Attributes\Request\Controller $controller
     * @param \Torugo\Router\Attributes\Request\Route $route
     * @param \ReflectionMethod $method
     * @throws \Torugo\Router\Exceptions\InvalidRouteException
     * @return void
     */
    private function addRoute(Controller $controller, Route $route, ReflectionMethod $method): void
    {
        $uri = "{$this->prefix}{$controller->getUri()}{$route->getUri()}";

        $this->checkIfRouteIsDuplicated($uri, $route->getRequestMethod());

        $this->routes[$route->getRequestMethod()->value][$uri] = [
            "controller" => $method->class,
            "method" => $method->name,
            "route" => $route
        ];
    }

    /**
     * Verifies if a route already exists in the routes list
     * @param string $uri
     * @param \Torugo\Router\Enums\RequestMethod $requestMethod
     * @throws \Torugo\Router\Exceptions\InvalidRouteException
     * @return void
     */
    private function checkIfRouteIsDuplicated(string $uri, RequestMethod $requestMethod)
    {
        if (array_key_exists($uri, $this->routes[$requestMethod->value] ?? [])) {
            throw new InvalidRouteException("Route '$uri' with method '{$requestMethod->value}' is duplicated.", 2);
        }
    }
}