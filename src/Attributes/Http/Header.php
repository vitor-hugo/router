<?php declare(strict_types=1);

namespace Torugo\Router\Attributes\Http;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Header
{
    /**
     * Defines a HTTP header
     * @param string $param Http Parameter
     * @param string $value Parameter's value
     */
    public function __construct(public string $param, public string $value)
    {
    }

    /**
     * Get the header as string
     * @return string
     */
    public function toString(): string
    {
        $this->param = rtrim($this->param, ": ");
        return "{$this->param}: {$this->value}";
    }
}
