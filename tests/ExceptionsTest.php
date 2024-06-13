<?php declare(strict_types=1);

namespace Tests\Router;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Router\Contracts\Controllers\DuplicatedRouteController;
use Tests\Router\Contracts\Controllers\InvalidController;
use Tests\Router\Contracts\Controllers\NotAController;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidControllerExeception;
use Torugo\Router\Exceptions\InvalidResponseException;
use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Router;

#[TestDox("Exceptions Tests")]
class ExceptionsTest extends TestCase
{
    private Router $router;
    private Client $client;

    public function setUp(): void
    {
        $this->router = new Router;
        $this->client = new Client(["base_uri" => "http://localhost:8000", "http_errors" => false]);
    }

    #[TestDox("Must throw InvalidRouteException when trying to set a invalid route prefix")]
    public function testShouldThrowOnInvalidRoutePrefix()
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("The route prefix '/invalid/prefix!' is invalid.");
        $this->router->setPrefix("/invalid/prefix!");
    }

    #[TestDox("Must throw InvalidRouteException when trying register duplicated routes.")]
    public function testShouldThrowOnRegisteringDuplicatedRoutes()
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("Route '/duplicated/route' with method 'get' is duplicated.");
        $this->router->register(DuplicatedRouteController::class);
    }

    #[TestDox("Must throw InvalidRouteException when a route is not found.")]
    public function testShouldThrowOnWhenARouteIsNotFound()
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("Route '/xyz' not found.");
        $this->router->resolve("/xyz", RequestMethod::GET);
    }

    #[TestDox("Must throw InvalidControllerException when trying register a nonexistent controller.")]
    public function testShouldThrowOnRegisteringNonexistentController()
    {
        $this->expectException(InvalidControllerExeception::class);
        $this->expectExceptionMessage("The controller 'Non\Existent\Controller' not found.");
        $this->router->register('Non\Existent\Controller');
    }

    #[TestDox("Must throw InvalidControllerException when trying register a invalid controller.")]
    public function testShouldThrowOnRegisteringInvalidController()
    {
        $this->expectException(InvalidControllerExeception::class);
        $this->expectExceptionMessage("The controller 'Tests\Router\Contracts\Controllers\NotAController' is invalid, controllers must use #[Controller()] attribute.");
        $this->router->register(NotAController::class);
    }

    #[TestDox("Must throw InvalidRouteException when trying to redirect to an invalid url.")]
    public function testShouldThrowWhenTryingToRedirectToInvalidUrl()
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("Invalid redirection URL.");
        $this->router->register(InvalidController::class);
        $this->router->resolve("/invalid/redirect", RequestMethod::GET);
    }

    #[TestDox("Must throw InvalidRouteException when trying to redirect with invalid status code.")]
    public function testShouldThrowWhenTryingToRedirectWithInvalidStatusCode()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage("The redirection status code '305' is invalid. Please, check documentation.");
        $this->router->register(InvalidController::class);
        $this->router->resolve("/invalid/redirect/code", RequestMethod::GET);
    }

    #[TestDox("Must throw InvalidResponseException defining a status code less than 100 or greater than 599.")]
    public function testShouldThrowWhenDefiningInvalidStatusCode()
    {
        $this->router->register(InvalidController::class);

        try {
            $this->router->resolve("/invalid/code1", RequestMethod::GET);
        } catch (\Throwable $th) {
            $exception = get_class($th);
            $this->assertEquals("Torugo\Router\Exceptions\InvalidResponseException", $exception);
            $this->assertEquals("The HTTP status code must be from 100 to 599, '99' received.", $th->getMessage());
        }

        try {
            $this->router->resolve("/invalid/code2", RequestMethod::GET);
        } catch (\Throwable $th) {
            $exception = get_class($th);
            $this->assertEquals("Torugo\Router\Exceptions\InvalidResponseException", $exception);
            $this->assertEquals("The HTTP status code must be from 100 to 599, '600' received.", $th->getMessage());
        }
    }

    #[TestDox("Must throw InvalidRequestMethod when receiving a invalid request method")]
    public function testShouldThrowWhenReceivingInvalidRequestMethod()
    {
        $response = $this->client->request("options", "/unit");
        $body = $response->getBody()->getContents();
        $this->assertEquals("The request method 'options' is not allowed.", $body);
    }
}
