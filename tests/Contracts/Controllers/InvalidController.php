<?php declare(strict_types=1);

namespace Tests\Router\Contracts\Controllers;

use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Get;
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
}
