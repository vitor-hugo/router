<?php declare(strict_types=1);

namespace Tests\Router\Contracts;

use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Delete;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Request\Patch;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Attributes\Request\Put;
use Torugo\Router\Attributes\Request\Route;
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

    #[Route('/patch', RequestMethod::PATCH)]
    public function patchRoute()
    {
        $requestData = Request::getData();
        return $requestData;
    }

    #[Route('/post', RequestMethod::POST)]
    public function postRoute()
    {
        $requestData = Request::getData();
        return $requestData;
    }

    #[Route('/put', RequestMethod::PUT)]
    public function putRoute()
    {
        $requestData = Request::getData();
        return $requestData;
    }
}
