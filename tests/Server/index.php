<?php declare(strict_types=1);

/**
 * This server is used to run the tests
 */

use Tests\Router\Contracts\UnitController;
use Torugo\Router\Request;
use Torugo\Router\Router;

require "../../vendor/autoload.php";

$router = new Router;

$router->registerMany([
    UnitController::class,
]);

try {
    $router->autoResolve();
} catch (\Throwable $th) {
    http_response_code(400);
    echo $th->getMessage();
}
