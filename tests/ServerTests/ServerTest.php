<?php declare(strict_types=1);

namespace Tests\Router\ServerTests;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox("Server Tests")]
#[Group("Server")]
class ServerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client(["base_uri" => "http://localhost:8000"]);
    }

    #[TestDox("Should redirect to '/index'")]
    public function testInsideRedirection()
    {
        $response = $this->client->request('GET', '/', ['allow_redirects' => true]);
        $body = $response->getBody()->getContents();
        $statusCode = $response->getStatusCode();
        $header = $response->getHeader("Content-Type")[0];

        $this->assertEquals("index", $body);
        $this->assertEquals(200, $statusCode);
        $this->assertEquals("text/plain;charset=UTF-8", $header);
    }

    #[TestDox("Should redirect to a external URL")]
    public function testOutsideRedirection()
    {
        $response = $this->client->request('GET', '/google', ['allow_redirects' => false]);
        $header = $response->getHeader('Location')[0];
        $statusCode = $response->getStatusCode();
        $this->assertEquals("https://google.com", $header);
        $this->assertEquals(302, $statusCode);
    }

    #[TestDox("Should perform a GET request")]
    public function testGetRequest(): void
    {
        $response = $this->client->request('GET', '/users');

        $header = $response->getHeader("Content-Type")[0];
        $this->assertEquals("application/json; charset=utf-8", $header);

        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey("data", $json);
        $this->assertCount(7, $json["data"]);
    }

    #[TestDox("Should perform a POST request")]
    public function testPostRequest(): void
    {
        $payload = [
            "id" => "8",
            "email" => "test8@email.com",
            "name" => "Test8"
        ];

        $response = $this->client->post('/users', ['form_params' => $payload]);
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        $this->assertArrayHasKey("data", $json);
        $this->assertSame($json["data"], $payload);
    }

    #[TestDox("Should perform a PUT request")]
    public function testPutRequest(): void
    {
        $payload = [
            "email" => "user7@email.com",
            "name" => "User7"
        ];

        $response = $this->client->put('/users/7', ['form_params' => $payload]);
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        $this->assertArrayHasKey("data", $json);
        $this->assertArrayIsIdenticalToArrayIgnoringListOfKeys($json["data"], $payload, ["id"]);
    }

    #[TestDox("Should perform a PATCH request")]
    public function testPatchRequest(): void
    {
        $payload = [
            "email" => "user7@email.com",
            "name" => "User7"
        ];

        $response = $this->client->patch('/users/7', ['form_params' => $payload]);
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        $this->assertArrayHasKey("data", $json);
        $this->assertArrayIsIdenticalToArrayIgnoringListOfKeys($json["data"], $payload, ["id"]);
    }

    #[TestDox("Should perform a DELETE request")]
    public function testDeleteRequest(): void
    {
        $response = $this->client->delete('/users/3');
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        $this->assertArrayHasKey("data", $json);
        $search = array_search("3", array_column($json["data"], "id"));
        $this->assertFalse($search);
    }

    #[TestDox("Should not send the default response")]
    public function testShouldNotSendDefaultResponse(): void
    {
        $response = $this->client->request("GET", "/users/avatar/12345");
        $text = $response->getBody()->getContents();
        $this->assertEquals("user avatar with id '12345'", $text);

        $header = $response->getHeader("Content-Type")[0];
        $this->assertEquals($header, "text/plain;charset=UTF-8");
    }
}
