<?php declare(strict_types=1);

namespace Tests\Router\Contracts\Controllers;

use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Response\HttpCode;
use Torugo\Router\Attributes\Response\Redirect;

#[Controller("/invalid")]
class InvalidController
{
    #[Get("/redirect")]
    #[Redirect("redirecting to a invalid url")]
    public function invalidRedirectUrl()
    {
        return "invalid";
    }

    #[Get("/redirect/code")]
    #[Redirect("/invalid/redirect", 305)]
    public function invalidRedirectStatusCode()
    {
        return "invalid status code";
    }

    #[Get("/code1")]
    #[HttpCode(99)]
    public function statusCode1()
    {
        return "invalid status code";
    }

    #[Get("/code2")]
    #[HttpCode(600)]
    public function statusCode2()
    {
        return "invalid status code";
    }
}
