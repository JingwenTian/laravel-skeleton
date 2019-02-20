<?php

namespace App\Exceptions;

use App\Support\Status\Status;

/**
 * Class InvalidArgumentException.
 *
 * @package App\Exceptions
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * InvalidArgumentException constructor.
     *
     * @param string|null $message
     * @param int|null    $code
     */
    public function __construct(string $message = null, int $code = null)
    {
        parent::__construct($message ?? '', $code ?? Status::CODE_COMMON_PARAMETER_MISSING);
    }
}
