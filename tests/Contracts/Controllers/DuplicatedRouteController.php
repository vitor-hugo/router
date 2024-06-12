<?php declare(strict_types=1);

namespace Tests\Router\Contracts\Controllers;

use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Get;

#[Controller("duplicated")]
class DuplicatedRouteController
{
    #[Get("route")]
    public function getRoute()
    {
        return "get route";
    }

    #[Get("route")]
    public function getAnoterRoute()
    {
        return "get another route";
    }
}
