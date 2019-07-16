<?php

/**
 * 守护任务管理配置.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2019/2/20 16:13
 */

namespace App\Console;

/**
 * Class DaemonOptions.
 *
 * @package App\Console
 */
class DaemonOptions
{
    /**
     * 允许消耗最大内存.
     *
     * @var int
     */
    public $memory;

    /**
     * 允许执行的最大时间.
     *
     * @var int
     */
    public $timeout;

    /**
     * 执行间断秒数.
     *
     * @var int
     */
    public $sleep;

    /**
     * Create a new worker options instance.
     *
     * @param float $sleep   任务执行间隔秒数
     * @param int   $timeout 作超时处理时间(秒) !!!需注意kafka consume消费或 redis brpop 等操作时设定的阻塞时间, 该超时时间需大于阻塞时间, 否则会导致进程无法往后执行
     * @param int   $memory  允许最大的内存占用数(M)
     */
    public function __construct(float $sleep = 0.1, int $timeout = 60, int $memory = 128)
    {
        $this->memory = $memory;
        $this->timeout = $timeout <= 0 ? 60 : $timeout;
        $this->sleep = $sleep;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode([
            'memory'    => $this->memory,
            'timeout'   => $this->timeout,
            'sleep'     => $this->sleep,
        ]);
    }
}
