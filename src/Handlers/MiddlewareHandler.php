<?php declare(strict_types=1);

namespace Torugo\Router\Handlers;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Torugo\Router\Attributes\Middleware;
use Torugo\Router\Models\Endpoint;

final class MiddlewareHandler
{
    public static function handle(Endpoint $endpoint): void
    {
        self::resolve($endpoint);
    }

    private static function resolve(Endpoint $endpoint): void
    {
        $middlewares = [
            ...self::getClassMiddlewares($endpoint),
            ...self::getMethodMiddlewares($endpoint)
        ];

        foreach ($middlewares as $middleware) {
            $instance = $middleware->newInstance();

            $class = $instance->middleware;
            $method = $instance->method;
            $args = $instance->args;

            $middleware = new $class();
            $middleware->{$method}(...$args);
        }
    }

    private static function getClassMiddlewares(Endpoint $endpoint): array
    {
        $reflection = new ReflectionClass($endpoint->getController());
        $attributes = $reflection->getAttributes(Middleware::class, ReflectionAttribute::IS_INSTANCEOF);
        return $attributes;
    }

    private static function getMethodMiddlewares(Endpoint $endpoint): array
    {
        $reflection = new ReflectionMethod($endpoint->getController(), $endpoint->getMethod());
        $attributes = $reflection->getAttributes(Middleware::class, ReflectionAttribute::IS_INSTANCEOF);
        return $attributes;
    }
}
