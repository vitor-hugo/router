<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Response;

use Attribute;
use Torugo\Router\Exceptions\InvalidResponseException;

#[Attribute(Attribute::TARGET_METHOD)]
class Redirect
{
    /**
     * Redirects a request to a defined URL
     * @param string $url Redirect location
     * @param int $statusCode Http Status Code, accepts only 300, 301, 302, 303, 304, 307 or 308. Default is 301.
     */
    public function __construct(public string $url, public int $statusCode = 301)
    {
        $this->checkHttpCode();
    }

    private function checkHttpCode(): void
    {
        $accepted = [300, 301, 302, 303, 304, 307, 308];
        if (!in_array($this->statusCode, $accepted, true)) {
            throw new InvalidResponseException("The redirection status code '{$this->statusCode}' is invalid. Please, check documentation.", 1);
        }
    }
}
