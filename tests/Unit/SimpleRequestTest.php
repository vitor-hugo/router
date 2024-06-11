<?php declare(strict_types=1);

namespace Tests\Router\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[Group("Unit")]
#[TestDox("Simple Request Tests")]
class SimpleRequestTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client(["base_uri" => "http://localhost:8000", "http_errors" => false]);
    }

    private function getResponseData(ResponseInterface $response): mixed
    {
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        return $data["data"] ?? [];
    }

    #[TestDox("Perform a simple DELETE request")]
    public function testSimpleDeleteRequest()
    {
        $response = $this->client->delete("/unit");
        $data = $this->getResponseData($response);
        $this->assertEquals("delete request", $data);
    }

    #[TestDox("Perform a simple GET request")]
    public function testSimpleGetRequest()
    {
        $response = $this->client->get("/unit");
        $data = $this->getResponseData($response);
        $this->assertEquals("get request", $data);
    }

    #[TestDox("Perform a simple PATCH request")]
    public function testSimplePatchRequest()
    {
        $response = $this->client->patch("/unit");
        $data = $this->getResponseData($response);
        $this->assertEquals("patch request", $data);
    }

    #[TestDox("Perform a simple POST request")]
    public function testSimplePostRequest()
    {
        $response = $this->client->post("/unit");
        $data = $this->getResponseData($response);
        $this->assertEquals("post request", $data);
    }

    #[TestDox("Perform a simple PUT request\n")]
    public function testSimplePutRequest()
    {
        $response = $this->client->put("/unit");
        $data = $this->getResponseData($response);
        $this->assertEquals("put request", $data);
    }

    #[TestDox("Perform a DELETE request with url parameter")]
    public function testParametrizedDeleteRequest()
    {
        $uuid = "c3ae0cd2-556f-434e-93a8-bfaa4c983856";
        $response = $this->client->delete("/unit/delete/$uuid");
        $data = $this->getResponseData($response);
        $this->assertEquals($uuid, $data);
    }

    #[TestDox("Perform a GET request with url parameter")]
    public function testParametrizedGetRequest()
    {
        $uuid = "1ef37401-d52a-4b9e-b10d-50731ad4d624";
        $response = $this->client->get("/unit/get/$uuid");
        $data = $this->getResponseData($response);
        $this->assertEquals($uuid, $data);
    }

    #[TestDox("Perform a POST request with form input")]
    public function testPostRequestWithFormInput()
    {
        $payload = ["first_name" => "Machado", "last_name" => "Assis"];

        $response = $this->client->post("/unit/post", ["form_params" => $payload]);
        $data = $this->getResponseData($response);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys($payload, $data, []);
    }

    #[TestDox("Perform a PATCH request with form input")]
    public function testPatchRequestWithFormInput()
    {
        $payload = ["first_name" => "Clarice", "last_name" => "Lispector"];

        $response = $this->client->patch("/unit/patch", ["form_params" => $payload]);
        $data = $this->getResponseData($response);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys($payload, $data, []);
    }

    #[TestDox("Perform a PUT request with form input")]
    public function testPutRequestWithFormInput()
    {
        $payload = ["first_name" => "Conceição", "last_name" => "Evaristo"];

        $response = $this->client->put("/unit/put", ["form_params" => $payload]);
        $data = $this->getResponseData($response);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys($payload, $data, []);
    }
}
