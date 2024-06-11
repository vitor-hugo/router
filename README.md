> [!NOTE]
> THIS PACKAGE IS UNDER DEVELOPMENT.

# Router <!-- omit in toc -->

A simple PHP router system.  
Uses the [PHP Attributes](https://www.php.net/manual/en/language.attributes.php) feature.  

# Table of Contents <!-- omit in toc -->

- [Requirements](#requirements)
- [Installing](#installing)
- [Usage](#usage)
  - [Routing](#routing)
  - [Registering Controllers](#registering-controllers)
  - [Resolving the requests](#resolving-the-requests)
    - [Auto Resolve](#auto-resolve)
    - [Resolving manually](#resolving-manually)
    - [Prefixing all routes](#prefixing-all-routes)
  - [Getting request data](#getting-request-data)
  - [Responses](#responses)
  - [Full example](#full-example)
- [Tests](#tests)
  - [With Makefile](#with-makefile)
  - [Manually](#manually)
- [Contribute](#contribute)
- [License](#license)

# Requirements

- PHP 8.2+
- Composer 2+ (Package manager)
- PHPUnit 11+ (Automated tests)

# Installing

```shell
composer require torugo/router
```

# Usage

## Routing

```php
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Get;

#[Controller("/users")]
class UsersController {
    #[Get()] // endpoint: GET /users
    public function findAll(): array
    {
        return [/* list of users */];
    }

    #[Get("/{id}")] // endpoint: GET /users/<the user id>
    public function findOne(string $id): array
    {
        return [/* user data */];
    }
}
```

## Registering Controllers

All controllers must use the Controller attribute.

```php
use Torugo\Router\Router;

$router = new Router;

$router->registerMany([
    UsersController::class,
    CostumersController::class,
    SuppliersController::class,
    StockController::class,
]);

try {
    $router->autoResolve();
} catch (\Throwable $th) {
    // Handle errors
}
```

Additionally you can register controllers individually.

```php
$router->register(UsersController::class);
$router->register(CostumersController::class);
$router->register(SuppliersController::class);
$router->register(StockController::class);
```

## Resolving the requests

### Auto Resolve

```php
try {
    $router->autoResolve();
} catch (\Throwable $th) {
    // Handle errors
}
```

### Resolving manually

```php
$uri = Request::getUri();
$requestMethod = Request::getMethod();

// Here you can filter the uri
// The request method must be a member of RequestMethod Enum

try {
    $router->resolve($uri, $requestMethod);
} catch (\Throwable $th) {
    // Handle errors
}
```

### Prefixing all routes

If your API has a prefixed route like `'/v1'` you can configure the router to handle it.

```php
use Torugo\Router\Router;

$router = new Router;
$router->setPrefix("/v1");

/**
 * If you receive a GET request with the URI '/v1/users/12345', the router will execute '/users/12345'
 */
```

## Getting request data

You can access the request data anywhere using the method `getData()` from `Torugo\Router\Request`.

```php
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Post;
use Torugo\Router\Request;

#[Controller("/users")]
class UsersController {
    #[Post()]
    public function add(): array
    {
        $userData = Request::getData();
        
        /* ADD NEW USER DATA ON DATABASE */
        
        return [/* new user data */];
    }
}
```

## Responses

When a controller method returns array it is converted to a Json string, other types are not converted.

```php
use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Get;

#[Controller("users")]
class UsersController {
    #[Get()]
    public function findAll(): array
    {
        $users = [
            [
                "id" => "1",
                "name" => "User 1",
                "email" => "user1@host.com"
            ],
            [
                "id" => "2",
                "name" => "User 2",
                "email" => "user2@host.com"
            ],
            [
                "id" => "3",
                "name" => "User 3",
                "email" => "user3@host.com"
            ],
        ];

        return $users;
        // '[{"id":"1","name":"User 1","email":"user1@host.com"},{"id":"2","name":"User ... ]'
    }
}
```

## Full example

- Controller class
    ```php
    <?php declare(strict_types=1);

    namespace MyApi\Modules\Users;

    use Torugo\Router\Attributes\Request\Controller;
    use Torugo\Router\Attributes\Request\Delete;
    use Torugo\Router\Attributes\Request\Get;
    use Torugo\Router\Attributes\Request\Post;
    use Torugo\Router\Attributes\Request\Put;

    #[Controller("/users")]
    class UsersController {
        #[Get()] // endpoint: GET /users
        public function findAll(): array
        {
            return [/* list of users */];
        }

        #[Get("/{id}")] // endpoint: GET /users/<the user id>
        public function findOne(string $id): array
        {
            return [/* user data */];
        }

        #[Delete("/{id}")] // endpoint: DELETE /users/<the user id>
        public function deleteUser(string $id): string
        {
            return "Deleted user with $id";
        }

        #[Post()] // endpoint: POST /users
        public function addUser(): array
        {
            return [/* new user data */];
        }

        #[Put("/{id}")] // endpoint: PUT /users/<the user id>
        public function updateUser(string $id): array
        {
            return [/* updated user data */];
        }
    }
    ```

- Main app file "index.php"
    ```php
    <?php declare(strict_types=1);
    
    use MyApi\Modules\Users\UsersController;
    use Torugo\Router\Router;

    require "../vendor/autoload.php";
    
    $router = new Router;

    $router->register(UsersController::class);
    // OR $router->register('MyApi\Modules\Users\UsersController');

    try {
        $router->autoResolve();
    } catch (\Throwable $th) {
        http_response_code(400);
        echo $th->getMessage();
    }
    ```

# Tests

## With Makefile

To run the tests with `make` use:

```
make test
```

or with PHPUnit testdox

```shell
make testdox
```

## Manually

> [!TIP]
> - You will need to use two terminal windows to run the tests manually.  
> - All commands must be executed from the project root.

### 1. Start Test Server <!-- omit in toc -->
In the first window terminal type:
```shell
php -S localhost\:8000 -t tests/ServerTests/Server/
```

### 2. Run the tests <!-- omit in toc -->
In the second window, from the project root type:
```shell
./vendor/bin/phpunit
```

# Contribute

It is currently not open to contributions, I intend to make it available as soon as possible.

# License

Router is licensed under the MIT License - see the LICENSE file for details.
