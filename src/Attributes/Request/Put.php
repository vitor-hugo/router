<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Request;

use Attribute;
use Torugo\Router\Attributes\Request\Route;
use Torugo\Router\Enums\RequestMethod;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Put extends Route
{
    public function __construct(string $uri = "")
    {
        parent::__construct($uri, RequestMethod::PUT);
    }
}
