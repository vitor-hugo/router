<?php declare(strict_types=1);

/**
 *
 * This file is used to run request tests
 *
 * From the root direct type: php -S localhost:8000 -t ./tests/Integration/Server/
 *
 */

use Tests\Router\Integration\Contracts\CostumersController;
use Tests\Router\Integration\Contracts\MainController;
use Tests\Router\Integration\Contracts\UsersController;
use Torugo\Router\Request;
use Torugo\Router\Response;
use Torugo\Router\Router;

require "../../../vendor/autoload.php";

$router = new Router;

$router->registerMany([
    MainController::class,
    UsersController::class,
    CostumersController::class,
]);

$uri = Request::getUri();
$reqMethod = Request::getMethod();

try {
    echo $router->resolve($uri, $reqMethod);
} catch (\Throwable $th) {
    Response::$data = $th->getMessage();
    echo Response::send();
}
