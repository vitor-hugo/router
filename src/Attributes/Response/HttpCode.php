<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Response;

use Attribute;
use Torugo\Router\Exceptions\InvalidResponseException;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpCode
{
    /**
     * Defines the HTTP status code for success responses
     * @param int $code Valid HTTP status code [100..599]
     */
    public function __construct(public int $code)
    {
        $this->validateHttpCode($this->code);
    }

    /**
     * Validates the HTTP Code
     * @param int $code
     * @throws \Torugo\Router\Exceptions\InvalidResponseException
     * @return void
     */
    public function validateHttpCode(int $code): void
    {
        if ($code < 100 || $code > 599) {
            throw new InvalidResponseException("The HTTP status code must be from 100 to 599, '$code' received.", 1);
        }
    }
}
