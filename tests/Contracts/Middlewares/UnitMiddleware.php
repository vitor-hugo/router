<?php declare(strict_types=1);

namespace Tests\Router\Contracts\Middlewares;

use Torugo\Router\Response;

class UnitMiddleware
{
    public function unit(string $data)
    {
        Response::include(["middleware" => $data]);
    }
}
