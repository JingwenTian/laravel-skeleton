<?php

namespace App\Exceptions;

use Exception;
use JsonSerializable;

/**
 * Class BaseException 业务异常处理.
 *
 * @package App\Exceptions
 */
class BaseException extends Exception implements JsonSerializable
{
    public const EXCEPTION_CONFIG_PREFIX = 'exceptions.';

    /**
     * BaseException constructor.
     *
     * @param string      $config
     * @param string|null $message
     * @param int|null    $code
     */
    public function __construct(string $config = '', string $message = null, int $code = null)
    {
        $exception = config(static::EXCEPTION_CONFIG_PREFIX.$config);
        $code = $code ?? ($exception['code'] ?? 0);
        $message = $message ?? ($exception['message'] ?? '');

        parent::__construct($message, $code);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }
}
