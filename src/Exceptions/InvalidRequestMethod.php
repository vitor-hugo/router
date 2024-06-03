<?php declare(strict_types=1);

namespace Torugo\Router\Exceptions;

use Exception;
use Throwable;

class InvalidRequestMethod extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->message = $message;
        $this->code = $code;

        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        if ($this->code > 0) {
            return "{$this->message} ({$this->code})";
        } else {
            return $this->message;
        }
    }
}
