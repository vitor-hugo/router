<?php declare(strict_types=1);

namespace Tests\Router\Contracts\Controllers;

use Tests\Router\Contracts\Middlewares\UnitMiddleware;
use Torugo\Router\Attributes\Middleware;
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Delete;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Request\Patch;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Attributes\Request\Put;
use Torugo\Router\Attributes\Request\Route;
use Torugo\Router\Attributes\Response\Header;
use Torugo\Router\Attributes\Response\HttpCode;
use Torugo\Router\Attributes\Response\NoResponse;
use Torugo\Router\Attributes\Response\Redirect;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Request;

#[Controller("unit")]
class UnitController
{
    #[Delete()]
    public function delete()
    {
        return "delete request";
    }

    #[Get()]
    public function get()
    {
        return "get request";
    }

    #[Patch()]
    public function patch()
    {
        return "patch request";
    }

    #[Post()]
    public function post()
    {
        return "post request";
    }

    #[Put()]
    public function put()
    {
        return "put request";
    }

    #[Route('/delete/{value}', RequestMethod::DELETE)]
    public function deleteRoute(string $value)
    {
        return $value;
    }

    #[Route('/get/{value}', RequestMethod::GET)]
    public function getRoute(string $value)
    {
        return $value;
    }

    #[Route('/multi/{param1}/{param2}', RequestMethod::GET)]
    public function multiParams(string $param1, string $param2)
    {
        return [$param1, $param2];
    }

    #[Route('/multi/{param1}/fixed/{param2}', RequestMethod::GET)]
    public function multiParamsWithFixed(string $param1, string $param2)
    {
        return [$param1, $param2];
    }

    #[Route('/patch/{id}', RequestMethod::PATCH)]
    public function patchRoute(string $id)
    {
        $requestData = Request::getData();
        $requestData["id"] = $id;
        return $requestData;
    }

    #[Route('/post', RequestMethod::POST)]
    public function postRoute()
    {
        $requestData = Request::getData();
        return $requestData;
    }

    #[Route('/put/{id}', RequestMethod::PUT)]
    public function putRoute(string $id)
    {
        $requestData = Request::getData();
        $requestData["id"] = $id;
        return $requestData;
    }

    #[Get("/middleware")]
    #[Middleware(UnitMiddleware::class, 'unit', ["data" => "This data was defined in a middleware"])]
    public function middleware()
    {
        return "this is de main data";
    }

    #[Get("/header")]
    #[Header("MyHeader", "My Header Content")]
    #[Header("OtherHeader", "This is another header")]
    public function header()
    {
        return "Testing header";
    }

    #[Post("/status")]
    #[HttpCode(256)]
    public function statusCode()
    {
        return "Testing HTTP status code";
    }

    #[Get("/redirect/inside")]
    #[Redirect('/unit')]
    public function redirectInside()
    {
        return "redirecting to inside route";
    }

    #[Get("/redirect/outside")]
    #[Redirect("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.min.json")]
    public function redirectOutside()
    {
        return "redirecting to inside route";
    }

    #[Get("/custom")]
    #[NoResponse()]
    #[Header("Content-Type", "image/png;base64")]
    public function sendCustomResponse()
    {
        echo "iVBORw0KGgoAAAANSUhEUgAAAQAAAAEACAIAAADTED8xAAADMElEQVR4nOzVwQnAIBQFQYXff81RUkQCOyDj1YOPnbXWPmeTRef+/3O/OyBjzh3CD95BfqICMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMK0CMO0TAAD//2Anhf4QtqobAAAAAElFTkSuQmCC";
    }
}
