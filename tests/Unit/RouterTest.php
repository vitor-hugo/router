<?php declare(strict_types=1);

namespace Tests\Router\Unit;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Router\Unit\Contracts\ValidControllerContract;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidRouteException;
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
    #[TestDox("Resolving GET Requests")]
    public function testResolveGetRequest(Router $router)
    {
        $response = $router->resolve('/users/234923', RequestMethod::GET);
        $this->assertEquals("Returns a user with id '234923'.", $response);

        $response = $router->resolve('/users', RequestMethod::GET);
        $this->assertEquals("Returns all users.", $response);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Resolving POST Requests")]
    public function testResolvePostRequest(Router $router)
    {
        $response = $router->resolve('/users', RequestMethod::POST);
        $this->assertEquals("Adds a new user.", $response);

        $response = $router->resolve('/users/search', RequestMethod::POST);
        $this->assertEquals("Returns a filtered list of users.", $response);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Resolving PUT Requests")]
    public function testResolvePutRequest(Router $router)
    {
        $response = $router->resolve('/users/2384789', RequestMethod::PUT);
        $this->assertEquals("Updates the user id '2384789'.", $response);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Resolving PATCH Requests")]
    public function testResolvePatchRequest(Router $router)
    {
        $response = $router->resolve('/users/status/23592783', RequestMethod::PATCH);
        $this->assertEquals("Changes the status of user '23592783'.", $response);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Resolving DELETE Requests")]
    public function testResolveDELETEhRequest(Router $router)
    {
        $response = $router->resolve('/users/234829', RequestMethod::DELETE);
        $this->assertEquals("Deactivates the user with id '234829'.", $response);
    }

    #[Depends("testControllerRegistration")]
    #[TestDox("Should Throw InvalidRouteException when route not found.")]
    public function testShouldThrowWhenRouteNotFound(Router $router)
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("Route '/users/not/exists' not found.");
        $router->resolve('/users/not/exists', RequestMethod::GET);
    }
}
