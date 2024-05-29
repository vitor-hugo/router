<?php declare(strict_types=1);

namespace Tests\Router\Unit;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Router;

class RouterTest extends TestCase
{
    #[TestDox("Should define a route prefix correctly")]
    public function testValidPrefix(): void
    {
        $router = new Router;
        $router->setPrefix("/v1");
        $this->assertEquals("/v1", $router->getPrefix());
    }

    #[TestDox("Should throw InvalidRouteException when defining an invalid route prefix")]
    public function testInvalidPrefix(): void
    {
        $this->expectException(InvalidRouteException::class);
        $this->expectExceptionMessage("The route prefix '/v1/%20' is invalid.");
        $router = new Router;
        $router->setPrefix("/v1/%20");
    }
}
