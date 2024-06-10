<?php declare(strict_types=1);

/**
 *
 * This file is used to run request tests
 *
 * From the root direct type: php -S localhost:8000 -t ./tests/ServerTests/Server/
 *
 */

use Tests\Router\ServerTests\Contracts\MainController;
use Tests\Router\ServerTests\Contracts\UsersController;
use Torugo\Router\Request;
use Torugo\Router\Response;
use Torugo\Router\Router;

require "../../../vendor/autoload.php";

$router = new Router;

$router->registerMany([
    MainController::class,
    UsersController::class,
]);

$uri = Request::getUri();
$reqMethod = Request::getMethod();

try {
    $router->resolve($uri, $reqMethod);
} catch (\Throwable $th) {
    http_response_code(400);
    echo $th->getMessage();
}
