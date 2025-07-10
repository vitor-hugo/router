<?php declare(strict_types=1);

namespace Tests\Router;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[TestDox("Request Tests")]
class RequestTest extends TestCase
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

        $response = $this->client->patch("/unit/patch/54321", ["form_params" => $payload]);
        $data = $this->getResponseData($response);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys($payload, $data, ["id"]);
        $this->assertEquals("54321", $data["id"]);
    }

    #[TestDox("Perform a PUT request with form input and url parameter\n")]
    public function testPutRequestWithFormInput()
    {
        $payload = ["first_name" => "Conceição", "last_name" => "Evaristo"];

        $response = $this->client->put("/unit/put/12345", ["form_params" => $payload]);
        $data = $this->getResponseData($response);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys($payload, $data, ["id"]);
        $this->assertEquals("12345", $data["id"]);
    }

    #[TestDox("Middleware must include some data in the response")]
    public function testMiddlewareIncludesData()
    {
        $response = $this->client->get("/unit/middleware");
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        $this->assertEquals("this is de main data", $json["data"]);
        $this->assertEquals("This data was defined in a middleware", $json["middleware"]);
    }

    #[TestDox("Header attribute must send headers correctly")]
    public function testResponseHeader()
    {
        $response = $this->client->get("/unit/header");
        $headers = $response->getHeaders();
        $body = $this->getResponseData($response);

        $this->assertArrayHasKey("MyHeader", $headers);
        $this->assertArrayHasKey("OtherHeader", $headers);

        $this->assertEquals("My Header Content", $headers["MyHeader"][0]);
        $this->assertEquals("This is another header", $headers["OtherHeader"][0]);

        $this->assertEquals("Testing header", $body);
    }

    #[TestDox("HttpCode attribute must set the status code correctly")]
    public function testHttpStatusCode()
    {
        $response = $this->client->post("/unit/status");
        $body = $this->getResponseData($response);

        $this->assertEquals(256, $response->getStatusCode());
        $this->assertEquals("Testing HTTP status code", $body);
    }

    public function testMustRedirectToInsideRoute()
    {
        $response = $this->client->get("/unit/redirect/inside", ['allow_redirects' => true]);
        $body = $this->getResponseData($response);
        $this->assertEquals("get request", $body);
    }

    public function testMustRedirectToOutsideRoute()
    {
        $response = $this->client->get("/unit/redirect/outside", ['allow_redirects' => true]);
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        $this->assertArrayHasKey("usd", $json);
        $this->assertArrayHasKey("brl", $json["usd"]);
    }

    public function testMustReceiveACustomResponse()
    {
        $response = $this->client->get("/unit/custom");
        $header = $response->getHeader("Content-Type");
        $this->assertEquals("image/png;base64", $header[0]);

        $img = $response->getBody()->getContents();
        $tempFilename = "/tmp/phpunit.testImage.testFetchWithTempFile";
        file_put_contents($tempFilename, base64_decode($img));
        $type = exif_imagetype($tempFilename);
        unlink($tempFilename);
        $this->assertTrue($type !== false);
        $this->assertEquals(IMAGETYPE_PNG, $type);
    }

    #[TestDox("Should resolve multi parameter route")]
    public function testMultipleParams()
    {
        $response = $this->client->get("/unit/multi/testA/testB");
        $data = $this->getResponseData($response);
        $this->assertIsArray($data);
        $this->assertTrue(count($data) == 2);
        $this->assertEquals($data[0], "testA");
        $this->assertEquals($data[1], "testB");
    }

    #[TestDox("Should resolve multi parameter route with fixed")]
    public function testMultipleParamsWithFixed()
    {
        $response = $this->client->get("/unit/multi/testA/fixed/testB");
        $data = $this->getResponseData($response);
        $this->assertIsArray($data);
        $this->assertTrue(count($data) == 2);
        $this->assertEquals($data[0], "testA");
        $this->assertEquals($data[1], "testB");
    }
}
