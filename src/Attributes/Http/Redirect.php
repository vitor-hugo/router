<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Http;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Redirect
{
    public function __construct(public string $url)
    {
    }
}
