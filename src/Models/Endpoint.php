<?php declare(strict_types=1);

namespace Torugo\Router\Models;

class Endpoint
{
    public function __construct(
        private string $controller,
        private string $method,
        private array $args = []
    ) {
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function execute(): mixed
    {
        $controller = new $this->controller();
        return $controller->{$this->method}(...$this->args);
    }
}
