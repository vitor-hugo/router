<?php declare(strict_types=1);

namespace Torugo\Router\Attributes;

use Attribute;
use Torugo\Router\Exceptions\InvalidMiddlewareException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Middleware
{
    public function __construct(
        public string $middleware,
        public string $method,
        public array $args = []
    ) {
        $this->validateMiddlewareClass();
        $this->validateMiddlewareMethod();
    }

    private function validateMiddlewareClass()
    {
        if (!class_exists($this->middleware)) {
            throw new InvalidMiddlewareException("Middleware '{$this->getMiddlewareName()}' not found.", 1);
        }
    }

    private function validateMiddlewareMethod()
    {
        if (!method_exists($this->middleware, $this->method)) {
            throw new InvalidMiddlewareException("The method '$this->method' does not exists in '{$this->getMiddlewareName()}'", 2);
        }
    }

    private function getMiddlewareName(): string
    {
        $arr = explode("\\", $this->middleware);
        return end($arr);
    }
}
