<?php declare(strict_types=1);

namespace Tests\Router\Unit;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Router\Unit\Contracts\DuplicatedRouteContract;
use Tests\Router\Unit\Contracts\ValidControllerContract;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Response;
use Torugo\Router\Router;

class RouterTest extends TestCase
{
    private Router $router;

    public function setup(): void
    {
        $this->router = new Router;
    }

    #[TestDox("Should define a route prefix correctly")]
    public function testValidPrefix(): void
    {
        $this->router->setPrefix("/v1");
        $this->assertEquals("/v1", $this->router->getPrefix());
    }

    #[TestDox("Should throw InvalidRouteException when defining an invalid route prefix")]
    public function testInvalidPrefix(): void
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("The route prefix '/v1/%20' is invalid.");
        $this->router->setPrefix("/v1/%20");
    }

    #[TestDox("Should register a controller correctly")]
    public function testControllerRegistration(): Router
    {
        $this->router->register(ValidControllerContract::class);
        $this->assertCount(5, $this->router->getRoutes());
        return $this->router;
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Should Throw InvalidRouteException when route not found.")]
    public function testShouldThrowWhenRouteNotFound(Router $router)
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("Route '/users/not/exists' not found.");
        $router->resolve('/users/not/exists', RequestMethod::GET);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Should set response's headers correctly")]
    public function testResponseHeaders(Router $router)
    {
        $router->resolve('/users', RequestMethod::POST);
        $this->assertCount(2, Response::$headers);
        $this->assertEquals("Content-Type: text/plain", Response::$headers[0]);
        $this->assertEquals("My-Header: My Header Content", Response::$headers[1]);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Should set response's http status code correctly")]
    public function testResponseCode(Router $router)
    {
        $router->resolve('/users', RequestMethod::POST);
        $this->assertEquals(201, Response::$httpStatusCode);
    }

    #[TestDox("Should throw InvalidRouteException when trying to register a duplicated route.")]
    public function testShouldThrowOnDuplicatedRoutes()
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("Route '/users/search' with method 'post' is duplicated.");
        $router = new Router;
        $router->registerMany([
            ValidControllerContract::class,
            DuplicatedRouteContract::class,
        ]);
    }
}
