<?php declare(strict_types=1);

namespace Torugo\Router;

use Torugo\Router\Exceptions\InvalidResponseException;

final class Response
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
        return self::sendJson();
    }

    /**
     * Defines a response header
     * @return void
     */
    public static function setHeader(string $header): void
    {
        header($header);
    }

    /**
     * Defines the response status code
     * @param int $code
     * @return void
     */
    public static function setStatusCode(int $code): void
    {
        http_response_code($code);
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

        self::$data = [];
        self::$includes = [];

        return $json;
    }
}
