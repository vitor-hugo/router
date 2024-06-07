<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Response;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Redirect
{
    /**
     * Redirects a request to a defined URL
     * @param string $url Redirect location
     * @param int $statusCode Http Status Code, accepts only 301, 302, 307 or 308. Default is 301.
     */
    public function __construct(public string $url, public int $statusCode = 301)
    {
        $this->checkHttpCode();
    }

    private function checkHttpCode(): void
    {
        $accepted = [301, 302, 307, 308];
        if (!in_array($this->statusCode, $accepted, true)) {
            $this->statusCode = 301;
        }
    }
}
