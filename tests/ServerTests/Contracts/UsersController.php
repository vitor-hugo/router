<?php declare(strict_types=1);

namespace Tests\Router\ServerTests\Contracts;

use Torugo\Router\Attributes\Response\Header;
use Torugo\Router\Attributes\Response\HttpCode;
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Delete;
use Torugo\Router\Attributes\Request\Get;
use Torugo\Router\Attributes\Request\Patch;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Attributes\Request\Put;
use Torugo\Router\Attributes\Response\NoResponse;
use Torugo\Router\Request;

#[Controller("/users")]
class UsersController
{
    public $users = [
        ["id" => "1", "email" => "test1@email.com", "name" => "Test1"],
        ["id" => "2", "email" => "test2@email.com", "name" => "Test2"],
        ["id" => "3", "email" => "test3@email.com", "name" => "Test3"],
        ["id" => "4", "email" => "test4@email.com", "name" => "Test4"],
        ["id" => "5", "email" => "test5@email.com", "name" => "Test5"],
        ["id" => "6", "email" => "test6@email.com", "name" => "Test6"],
        ["id" => "7", "email" => "test7@email.com", "name" => "Test7"],
    ];

    #[Get()]
    public function getAll(): array
    {
        return $this->users;
    }

    #[Get("/{id}")]
    public function findOne(string $id): array
    {
        return array_filter($this->users, function ($user) use ($id) {
            return $user["id"] == $id;
        });
    }

    #[Post()]
    #[HttpCode(201)]
    public function add()
    {
        $user = Request::getData();
        $this->users[] = $user;
        return end($this->users);
    }

    #[Put("/{id}")]
    #[Patch("/{id}")]
    #[HttpCode(200)]
    public function update(string $id)
    {
        $payload = Request::getData();
        foreach ($this->users as $index => $user) {
            if ($user["id"] === $id) {
                $this->users[$index]["email"] = $payload["email"] ?? $this->$user[$index]["email"];
                $this->users[$index]["name"] = $payload["name"] ?? $this->$user[$index]["name"];
                break;
            }
        }

        return $this->users[$index];
    }

    #[Delete("/{id}")]
    #[HttpCode(200)]
    public function delete(string $id)
    {
        foreach ($this->users as $index => $user) {
            if ($user["id"] === $id) {
                unset($this->users[$index]);
                break;
            }
        }

        return $this->users;
    }

    #[Get("/avatar/{id}")]
    #[NoResponse()]
    #[Header("Content-Type", "image/png")]
    #[Header("MyHeader", "Custom Header")]
    #[HttpCode(200)]
    public function getAvatarImage(string $id)
    {
        echo "user avatar with id '$id'";
    }
}
