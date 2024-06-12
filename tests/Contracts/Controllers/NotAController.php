<?php declare(strict_types=1);

namespace Tests\Router\Contracts\Controllers;

use Torugo\Router\Attributes\Request\Get;

class NotAController
{
    #[Get()]
    public function get()
    {
        return "get request";
    }
}
