<?php
/**
 * 守护任务管理.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2019/2/20 16:13
 */

namespace App\Console;

use App\Support\Constant\ELogTopicConst;

/**
 * Class DataBusService.
 *
 * @example
 *      $options = new DaemonOptions($sleep, $timeout, $limit, $memory);
 *      app(DaemonManager::class)->daemon($options, new OrderRefundProcess());
 * @doc 注意: 当前守护任务必须有 Supervisord 进行守护, 因为平滑重启的机制是: 进程平滑终止+supervisord重新唤起
 *
 * @package App\Console
 */
class DaemonManager
{
    /**
     * 人工指示的退出信号.
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * 进程最大执行次数.
     *
     * @var int
     */
    public $maxRequests = 0;

    /**
     * Listen to the given job in a loop.
     *
     * 1. 通过此方法统一管理运行后台守护任务
     * 2. 实现平滑重启进程机制 (依赖 SIGTERM 信号和 supervisord)
     * 3. 实现超过最大执行次数重启进程机制(无需额外重启机制)
     * 4. 实现超过最大执行时间强制终止进程机制(合理设置超时时间)
     * 5. 实现超过内存限制时重启进程机制(避免内存溢出)
     *
     * @param DaemonOptions   $options
     * @param DaemonInterface $processor
     */
    public function daemon(DaemonOptions $options, DaemonInterface $processor): void
    {
        // 获取队列连接, 大部分守护任务都是消费队列的任务, 所以需要实现此方法来实现连接的创建
        // 如果单纯的任务守护无需初始化, 则忽略
        $queueManager = $processor->getQueueManager();

        // 监听终止信号, 用于平滑重启进程
        if ($this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $this->maxRequests = 0;

        while (true) {
            try {
                // 从队列中消费取出消息, 然后交给 process() 方法处理
                // 如果不存在从队列中消费消息, 则返回空数组即可
                $message = $processor->getQueueMessage($queueManager);

                // 注册超时处理, 超时后终止当前进程
                // 考虑到 kafka consume 消息会最多阻塞120秒, 此处的超时最少设置为180秒
                if ($this->supportsAsyncSignals()) {
                    $this->registerTimeoutHandler($options);
                }

                // 执行任务逻辑
                $processor->process($message);
            } catch (\Throwable $e) {
                $this->output('daemon process exception', ['exception' => $e, 'options' => $options, 'params' => $message ?? []]);
            }

            // 执行次数计数
            ++$this->maxRequests;

            // 执行间断睡眠
            $this->sleep($options->sleep);

            // 当收到终止的信号或内存超过限制, 会终止当前进程
            $this->stopIfNecessary($options);
        }
    }

    /**
     * Determine if "async" signals are supported.
     *
     * @return bool
     */
    protected function supportsAsyncSignals()
    {
        return extension_loaded('pcntl');
    }

    /**
     * Enable async signals for the process.
     *
     * kill -s SIGTERM <pid> 通过SIGTERM信号进行平滑终止(由supervisord 重新唤起)
     */
    protected function listenForSignals()
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, function () {
            $this->shouldQuit = true;
        });
    }

    /**
     * Register the worker timeout handler.
     *
     * @param DaemonOptions $options
     */
    protected function registerTimeoutHandler(DaemonOptions $options)
    {
        pcntl_signal(SIGALRM, function () use ($options) {
            $this->output('process timeout killed', ['options' => $options]);
            $this->kill(1);
        });
        pcntl_alarm(max($options->timeout, 0));
    }

    /**
     * Stop the process if necessary.
     *
     * @param DaemonOptions $options
     */
    protected function stopIfNecessary(DaemonOptions $options)
    {
        if ($this->shouldQuit) {
            $this->output('process smooth stoped', ['options' => $options]);
            $this->stop();
        } elseif ($options->limit && $options->limit < $this->maxRequests) {
            $this->output('process maximum executions stoped', ['options' => $options]);
            $this->stop();
        } elseif ($this->memoryExceeded($options->memory)) {
            $this->output('process memery exceeded stoped', ['options' => $options]);
            $this->stop(12);
        }
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param int $memoryLimit
     *
     * @return bool
     */
    protected function memoryExceeded(int $memoryLimit)
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param int $status
     */
    protected function stop(int $status = 0)
    {
        exit($status);
    }

    /**
     * Kill the process.
     *
     * @param int $status
     */
    protected function kill(int $status = 0)
    {
        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }
        exit($status);
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param int|float $seconds
     */
    protected function sleep($seconds)
    {
        if ($seconds < 1) {
            usleep($seconds * 1000000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * Write a string as notice output.
     *
     * @param string $string
     * @param array  $details
     */
    protected function output(string $string, array $details = []): void
    {
        echo '['.date('Y-m-d H:i:s').'] '.$string.PHP_EOL;
        app()->elog->notice(ELogTopicConst::TOPIC_CONSOLE, '[console]'.$string, $details);
    }
}
