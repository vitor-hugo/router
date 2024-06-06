<?php declare(strict_types=1);

namespace Tests\Router\Unit\Contracts;

use Torugo\Router\Attributes\Request\Controller;
use Torugo\Router\Attributes\Request\Post;

#[Controller("users")]
class DuplicatedRouteContract
{
    #[Post("/search")]
    public function method1()
    {
        return "Testing duplicated routes";
    }
}
