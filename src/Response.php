<?php declare(strict_types=1);

namespace Torugo\Router;

use Torugo\Router\Exceptions\InvalidResponseException;

class Response
{
    /**
     * Response data, it is recommended to send arrays
     * @var mixed
     */
    private static mixed $data = [];

    /**
     * Should include in response body, e.g. refresh tokens
     * @var array
     */
    private static array $includes = [];

    /**
     * Response's HTTP status code
     * @var int
     */
    public static int $httpStatusCode = 200;

    /**
     * Should include headers strings. E.g. "Server: ProvaJa/Api", "Content-Type: text/html; charset=utf-8"
     * @var array Array of strings
     */
    public static $headers = [];

    /**
     * Data to be included on the JSON response body
     * @param array $data
     * @return void
     */
    public static function include(array $data): void
    {
        self::$includes = [...$data];
    }

    /**
     * Sends the response, if the data type is array it will be sent as JSON,
     * in other cases will be sent as RAW data
     * @param mixed $data
     * @return mixed
     */
    public static function send(mixed $data): mixed
    {
        self::$data = $data;

        http_response_code(self::$httpStatusCode);
        self::setHeaders();

        if (gettype(self::$data) == "array") {
            return self::sendJson();
        } else {
            // Send raw data
            return self::$data;
        }
    }

    /**
     * Defines the response headers
     * @return void
     */
    private static function setHeaders(): void
    {
        foreach (self::$headers as $header) {
            if (gettype($header) !== "string") {
                continue;
            }

            header($header);
        }
    }

    /**
     * Sends the response as JSON string
     * @throws \Torugo\Router\Exceptions\InvalidResponseException
     * @return string
     */
    private static function sendJson(): string
    {
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'data' => self::$data,
            ...self::$includes
        ];

        $json = @json_encode($response, JSON_UNESCAPED_UNICODE);

        if ($json == false || $json == null) {
            throw new InvalidResponseException("Unable to convert response to JSON", 2);
        }

        return $json;
    }
}
