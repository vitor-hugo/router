<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Http;

use Attribute;

/**
 * Avoids sending the Router default responses.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class NoResponse
{
}
