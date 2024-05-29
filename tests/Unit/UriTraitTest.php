<?php declare(strict_types=1);

namespace Tests\Router\Unit;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Router\Unit\Contracts\UriTestContract;

#[TestDox("UriTrait Tests")]
class UriTraitTest extends TestCase
{
    #[TestDox("Should normalize URI")]
    public function testNormalizeUri(): void
    {
        $stub = new UriTestContract;
        $this->assertEquals("/v1/user/ea83d03a-1845", $stub->getNormalizedUri("v1/user/ea83d03a-1845/"));
    }

    #[TestDox("URI Should be valid")]
    public function testUriShouldBeValid()
    {
        $stub = new UriTestContract;
        $this->assertTrue($stub->getValidatedUri("/v1/user/ea83d03a-1845"));
    }

    #[TestDox("Should return FALSE when URI is invalid")]
    public function testShouldThrowWhenUriIsInvalid()
    {
        $stub = new UriTestContract;
        $this->assertFalse($stub->getValidatedUri("/v1/user?id=ea83d03a-1845"));
    }
}
