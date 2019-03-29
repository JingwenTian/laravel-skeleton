<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SampleJobs.
 *
 * @doc 异步队列中执行，而不是触发时立即执行.
 * @doc 使用 supervisord 守护队列任务: php artisan queue:work --timeout=10 --sleep=3 --tries=3
 * @doc 如某些任务需要单独队列执行(如耗时的任务), 可以单独起一个队列进行
 *      php artisan queue:work --queue={batch-email} --timeout=10 --sleep=3 --tries=3
 * @doc 分发消息
 *      SampleJobs::dispatch($userItem);
 *      SampleJobs::dispatch($userItem)->delay(now()->addSeconds(10)); // 延时分发
 *      SampleJobs::dispatch($userItem)->onQueue('{batch-email}'); // 往指定的队列分发
 *
 * @package App\Jobs
 */
class SampleJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 2;

    /**
     * @var User
     */
    protected $user;

    /**
     * 创建一个新的任务实例.
     *
     * @param User $user
     * @param int  $delay
     */
    public function __construct(User $user/*, int $delay*/)
    {
        $this->user = $user;

        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        //$this->delay($delay);
    }

    /**
     * 运行任务.
     */
    public function handle()
    {
        if ($this->user->name === 'laravel') {
            return;
        }

        $this->user->name = 'update by jobs';
        $this->user->save();
    }

    /**
     * 要处理的失败任务.
     *
     * @param Exception $exception
     */
    public function failed(Exception $exception)
    {
        // handle() 运行任务时出现未捕获的异常等情况, 该任务会失败, 进入failed()
        // 记录异常日志 ['exception' => $exception, 'params' => $this->user]
    }
}
