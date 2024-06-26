<?php declare(strict_types=1);

namespace Torugo\Router\Enums;

use Torugo\Router\Exceptions\InvalidRequestMethod;

enum RequestMethod: string
{
    case DELETE = 'delete';
    case GET = 'get';
    case PATCH = 'patch';
    case POST = 'post';
    case PUT = 'put';

    public static function tryFromRequest(): static
    {
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD'] ?? "get");
        $case = self::tryFrom($requestMethod);

        if (!$case) {
            throw new InvalidRequestMethod("The request method '$requestMethod' is not allowed.", 1);
        }

        return $case;
    }
}
