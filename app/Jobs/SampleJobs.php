<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SampleJobs.
 *
 * @doc 异步队列中执行，而不是触发时立即执行.
 *
 * @package App\Jobs
 */
class SampleJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
}
