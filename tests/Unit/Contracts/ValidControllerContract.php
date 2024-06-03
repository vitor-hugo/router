<?php declare(strict_types=1);

namespace Tests\Router\Unit\Contracts;

use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Delete;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Request\Patch;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Attributes\Request\Put;

#[Controller("users")]
class ValidControllerContract
{
    #[Get("{id}")]
    public function findOne(string $id)
    {
        return "Returns a single user data";
    }

    #[Get()]
    public function findAll()
    {
        return "Retuns all users";
    }

    #[Post("search")]
    public function searchUsers()
    {
        return "Returns a filtered list of users";
    }

    #[Post()]
    public function addUser()
    {
        return "Adds a new user";
    }

    #[Put("{id}")]
    public function updateUser(string $id)
    {
        return "Updates an user data";
    }

    #[Delete("{id}")]
    public function deleteUser(string $id)
    {
        return "Deactivates an user from database";
    }

    #[Patch("/status/{id}")]
    public function changeUserStatus(string $id)
    {
        return "Changes a user status";
    }
}
