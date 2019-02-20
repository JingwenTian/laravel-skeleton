<?php

namespace App\Exceptions;

use App\Support\Status\Status;

/**
 * Class UnauthorizedException 未鉴权异常(未登录).
 *
 * @package App\Exceptions
 */
class UnauthorizedException extends RuntimeException
{
    public function __construct(string $message = '', $code = null)
    {
        parent::__construct($message, $code ?? Status::CODE_COMMON_UNAUTHORIZED);
    }
}
