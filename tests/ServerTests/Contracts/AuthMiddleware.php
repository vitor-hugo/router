<?php declare(strict_types=1);

namespace Tests\Router\ServerTests\Contracts;

use Torugo\Router\Response;

class AuthMiddleware
{
    public function isAuthenticated(bool $isAuth): void
    {
        if ($isAuth == false) {
            throw new \Exception("User is not authenticated");
        }
    }

    public function renewToken(): void
    {
        Response::include(["token" => "renewed token"]);
    }
}
