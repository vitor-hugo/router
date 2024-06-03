<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Request;

use Attribute;
use Torugo\Router\Exceptions\InvalidRouteException;
use Torugo\Router\Traits\UriTrait;

#[Attribute(Attribute::TARGET_CLASS)]
class Controller
{
    use UriTrait;

    public function __construct(private string $uri = "")
    {
        $this->uri = $this->normalizeUri($this->uri);
        $this->validate();
    }

    private function validate()
    {
        if ($this->validateUri($this->uri) == false) {
            throw new InvalidRouteException("The URI '{$this->uri}' is invalid.", 1);
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
