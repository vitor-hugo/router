<?php declare(strict_types=1);

namespace Tests\Router\ServerTests\Contracts;

use Torugo\Router\Attributes\Http\Header;
use Torugo\Router\Attributes\Http\Redirect;
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Delete;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Request\Patch;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Attributes\Request\Put;

#[Controller()]
class MainController
{
    #[Get()]
    #[Post()]
    #[Put()]
    #[Patch()]
    #[Delete()]
    #[Redirect("/index", 301)]
    public function main()
    {
    }

    #[Get("/index")]
    #[Header("Content-Type", "text/plain;charset=UTF-8")]
    public function index()
    {
        return "index";
    }

    #[Get("/google")]
    #[Redirect("https://google.com", 302)]
    public function redirectToGoole()
    {
    }
}
