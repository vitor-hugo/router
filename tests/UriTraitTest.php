<?php declare(strict_types=1);

namespace Tests\Router;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Torugo\Router\Traits\UriTrait;

#[TestDox("URI Trait Tests")]
class UriTraitTest extends TestCase
{
    use UriTrait;

    #[TestDox("Must normalize an URI correctly")]
    public function testNormalizeUriMethod()
    {
        $uri = $this->normalizeUri("costumer/report/5be9d902/");
        $this->assertEquals("/costumer/report/5be9d902", $uri);

        $uri = $this->normalizeUri("user//////5be9d902///////////////");
        $this->assertEquals("/user/5be9d902", $uri);
    }

    #[TestDox("Must validate a REQUEST URI")]
    public function testValidateUriMethod()
    {
        $validation = $this->validateUri("/costumer/report/5be9d902");
        $this->assertTrue($validation);

        $validation = $this->validateUri("/user?id=5be9d902");
        $this->assertFalse($validation);

        $validation = $this->validateUri("/user/{id}");
        $this->assertFalse($validation);
    }

    #[TestDox("Must validate a ROUTE URI")]
    public function testValidateRouteUri()
    {
        $validation = $this->validateRouteUri("/user/{id}");
        $this->assertTrue($validation);

        $validation = $this->validateRouteUri("/user/@id");
        $this->assertFalse($validation);
    }
}
