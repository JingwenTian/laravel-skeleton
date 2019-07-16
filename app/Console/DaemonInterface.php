<?php

/**
 * 守护任务管理接口类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2019/2/20 16:13
 */

namespace App\Console;

/**
 * Interface DaemonInterface.
 *
 * @package App\Console
 */
interface DaemonInterface
{
    /**
     * 获取队列处理器.
     */
    public function getQueueManager();

    /**
     * 获取队列消息.
     *
     * @param $manager
     *
     * @return array
     */
    public function getQueueMessage($manager): array;

    /**
     * 执行任务.
     *
     * @param array $message
     */
    public function process(array $message);
}
