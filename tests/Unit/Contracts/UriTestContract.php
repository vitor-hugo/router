<?php declare(strict_types=1);

namespace Tests\Router\Unit\Contracts;

use Torugo\Router\Traits\UriTrait;

class UriTestContract
{
    use UriTrait;

    public function getNormalizedUri(string $uri): string
    {
        return $this->normalizeUri($uri);
    }

    public function getValidatedUri(string $uri): bool
    {
        return $this->validateUri($uri);
    }
}
