<?php declare(strict_types=1);

namespace Torugo\Router;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Torugo\Router\Attributes\Response\Header;
use Torugo\Router\Attributes\Response\HttpCode;
use Torugo\Router\Attributes\Response\Redirect;
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Route;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidControllerExeception;
use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Models\Endpoint;
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

    // MARK: Routes Registration

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
     * Add a Controller routes to the router list
     * @param \Torugo\Router\Attributes\Request\Controller $controller
     * @param \Torugo\Router\Attributes\Request\Route $route
     * @param \ReflectionMethod $method
     * @throws \Torugo\Router\Exceptions\InvalidRouteException
     * @return void
     */
    private function addRoute(Controller $controller, Route $route, ReflectionMethod $method): void
    {
        $uri = "{$this->prefix}{$controller->getUri()}{$route->getUri()}";
        $route->setUri($uri);

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

    ///////////////////////////////////////////////////////////////////////////////////
    // MARK: Route Resolve
    ///////////////////////////////////////////////////////////////////////////////////

    /**
     * This method uses the current URI and Request Method withou any filter, if you need
     * to filter the URI, use the 'resolve' method
     * @return mixed
     */
    public function autoResolve(): void
    {
        $uri = Request::getUri();
        $reqMethod = Request::getMethod();
        $this->resolve($uri, $reqMethod);
    }

    /**
     * Resolves the request uri and method, calling the correct endpoint (controler->method(args)).
     * @param string $uri Request's URI
     * @param \Torugo\Router\Enums\RequestMethod $requestMethod
     * @throws \Torugo\Router\Exceptions\InvalidRouteException
     * @return mixed
     */
    public function resolve(string $uri, RequestMethod $requestMethod): void
    {
        $endpoint = $this->findEndpoint($uri, $requestMethod);

        if ($endpoint == false) {
            throw new InvalidRouteException("Route '$uri' not found.", 3);
        }

        $this->sendResponse($endpoint);
    }

    /**
     * Searches and returns the correct endpoint to be executed, returns false if not found.
     * @param string $uri Request's uri
     * @param \Torugo\Router\Enums\RequestMethod $requestMethod
     * @return \Torugo\Router\Models\Endpoint|false
     */
    public function findEndpoint(string $uri, RequestMethod $requestMethod): Endpoint|false
    {
        $uri = $this->normalizeUri($uri);

        if (array_key_exists($requestMethod->value, $this->routes) == false) {
            return false;
        }


        foreach ($this->routes[$requestMethod->value] as $route) {
            $key = $this->requestUriMatches($uri, $route["route"], $args);

            if ($key) {
                $endpoint = $this->routes[$requestMethod->value][$key];
                return new Endpoint($endpoint["controller"], $endpoint["method"], $args ?? []);
            }

        }

        return false;
    }

    /**
     * Checks if the request's uri matches a route in the routes list
     * @param string $uri Request's uri
     * @param \Torugo\Router\Attributes\Request\Route $route
     * @param array|null $args
     * @return string|false
     */
    private function requestUriMatches(string $uri, Route $route, array|null &$args = []): string|false
    {
        $requestUriArray = $this->getUriParts($uri);
        $routeUriArray = $this->getUriParts($route->getUri());

        if (count($requestUriArray) !== count($routeUriArray)) {
            return false;
        }

        foreach ($routeUriArray as $index => $uriPart) {
            if (!isset($requestUriArray[$index])) {
                return false;
            }

            if ($uriPart === $requestUriArray[$index]) {
                continue;
            }

            if (str_starts_with($uriPart, "{")) {
                $routeParameter = explode(' ', preg_replace('/{([\w\-%]+)(<(.+)>)?}/', '$1 $3', $uriPart));
                $argName = $routeParameter[0];
                $argRegExp = (empty($routeParameter[1]) ? '[\w\-\@]+' : $routeParameter[1]);

                if (preg_match('/^' . $argRegExp . '$/', $requestUriArray[$index])) {
                    $args[$argName] = $requestUriArray[$index];
                    continue;
                }
            } else {
                return false;
            }
        }

        return $route->getUri();
    }

    /**
     * Explodes an URI and retuns it as an array of URI parts
     * @param string $uri
     * @return array
     */
    private function getUriParts(string $uri): array
    {
        $parts = explode("/", $uri);
        $parts = array_values(array_filter($parts, 'strlen'));
        return $parts;
    }

    ///////////////////////////////////////////////////////////////////////////////////
    // MARK: Response
    ///////////////////////////////////////////////////////////////////////////////////

    /**
     * Sends the response data
     * @param \Torugo\Router\Models\Endpoint $endpoint
     * @return void
     */
    private function sendResponse(Endpoint $endpoint): void
    {
        $this->redirectIfNecessary($endpoint);
        $this->setResponseHttpCode($endpoint);
        $this->setResponseHeaders($endpoint);

        $data = [];

        try {
            $data = $endpoint->execute() ?? [];
        } catch (\Throwable $th) {
            throw $th;
        }

        echo Response::send($data);
    }

    private function redirectIfNecessary(Endpoint $endpoint): void
    {
        $refMethod = new ReflectionMethod($endpoint->getController(), $endpoint->getMethod());
        $attributes = $refMethod->getAttributes(Redirect::class, ReflectionAttribute::IS_INSTANCEOF);

        if (count($attributes) != 1) {
            return;
        }

        $redirect = $attributes[0]->newInstance();
        Request::redirect($redirect->url, $redirect->statusCode);
    }

    /**
     * Summary of getResponseHttpCode
     * @param \Torugo\Router\Models\Endpoint $endpoint
     * @return int
     */
    private function setResponseHttpCode(Endpoint $endpoint): void
    {
        $refMethod = new ReflectionMethod($endpoint->getController(), $endpoint->getMethod());
        $attributes = $refMethod->getAttributes(HttpCode::class, ReflectionAttribute::IS_INSTANCEOF);

        if (count($attributes) != 1) {
            Response::$httpStatusCode = 200;
            return;
        }

        Response::$httpStatusCode = $attributes[0]->newInstance()->code;
    }

    /**
     * Summary of setResponseHeaders
     * @param \Torugo\Router\Models\Endpoint $endpoint
     * @return void
     */
    private function setResponseHeaders(Endpoint $endpoint): void
    {
        $refMethod = new ReflectionMethod($endpoint->getController(), $endpoint->getMethod());
        $headers = $refMethod->getAttributes(Header::class, ReflectionAttribute::IS_INSTANCEOF);

        foreach ($headers as $header) {
            $header = $header->newInstance()->toString();
            if (!in_array($header, Response::$headers)) {
                Response::$headers[] = $header;
            }
        }
    }
}
