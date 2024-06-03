<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Request;

use Attribute;
use Torugo\Router\Enums\RequestMethod;
use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Traits\UriTrait;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    use UriTrait;

    public function __construct(
        private string $uri,
        private RequestMethod $requestMethod
    ) {
        $this->uri = $this->normalizeUri($this->uri);
        $this->validate();
    }

    private function validate()
    {
        if ($this->validateRouteUri($this->uri) == false) {
            throw new InvalidRouteException("The URI '{$this->uri}' is invalid.", 1);
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
        $this->validate();
    }

    public function getRequestMethod(): RequestMethod
    {
        return $this->requestMethod;
    }
}
