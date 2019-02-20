<?php

namespace App\Exceptions;

use App\Support\Status\Status;

/**
 * Class NetworkUnavailableException 网络请求异常.
 *
 * @package App\Exceptions
 */
class NetworkUnavailableException extends RuntimeException
{
    /**
     * NetworkUnavailableException constructor.
     *
     * @param string|null $message
     * @param int|null    $code
     */
    public function __construct(string $message = null, int $code = null)
    {
        parent::__construct($message ?? '', $code ?? Status::CODE_COMMON_RPC_ERROR);
    }
}
