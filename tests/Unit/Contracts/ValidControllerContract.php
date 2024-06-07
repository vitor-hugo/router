<?php declare(strict_types=1);

namespace Tests\Router\Unit\Contracts;

use Torugo\Router\Attributes\Response\Header;
use Torugo\Router\Attributes\Response\HttpCode;
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Delete;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Request\Patch;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Attributes\Request\Put;

#[Controller("users")]
class ValidControllerContract
{
    #[Get("/noresponse")]
    public function noResponse()
    {
    }

    #[Get("/{id}")]
    public function findOne(string $id)
    {
        return "Returns a user with id '$id'.";
    }

    #[Get()]
    public function findAll()
    {
        return "Returns all users.";
    }

    #[Post("/search")]
    public function searchUsers()
    {
        return "Returns a filtered list of users.";
    }

    #[Post()]
    #[Header("Content-Type", "text/plain")]
    #[Header("My-Header", "My Header Content")]
    #[Header("My-Header", "My Header Content")] // duplicated on purpose
    #[HttpCode(201)]
    public function addUser()
    {
        return "Adds a new user.";
    }

    #[Put("/{id}")]
    public function updateUser(string $id)
    {
        return "Updates the user id '$id'.";
    }

    #[Delete("/{id}")]
    public function deleteUser(string $id)
    {
        return "Deactivates the user with id '$id'.";
    }

    #[Patch("/status/{id}")]
    public function changeUserStatus(string $id)
    {
        return "Changes the status of user '$id'.";
    }
}
