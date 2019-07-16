<?php

namespace App\Console\Commands;

use App\Console\DaemonInterface;
use App\Console\DaemonManager;
use App\Console\DaemonOptions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Redis;

class SampleDaemonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:daemon-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('run daemon job: '.Carbon::now());

        // Mock 队列数据
        $redis = Redis::connection();
        for ($i = 0; $i <= 100; ++$i) {
            $redis->lpush('test-queue', json_encode(['i' => $i, 't' => (string) Carbon::now()]));
        }

        // 处理器逻辑
        $processor = new class() implements DaemonInterface {
            public function getQueueManager()
            {
                return Redis::connection();
            }

            public function getQueueMessage($manager): array
            {
                $message = $manager->brpop('test-queue', 2);

                return json_decode($message[1] ?? '[]', true);
            }

            public function process(array $message)
            {
                sleep(1);
                $message && print_r($message);
            }
        };

        // 执行守护任务
        // 通过调整 timeout 时间来模拟超时后进程kill停止(任务未执行完直接退出)
        // 通过调整消耗的 memory 和限制的 memery 来模拟内存超限后的进程平滑终止
        // 通过给进程发送 SIGTERM 信号来模拟进程平滑终止 kill -s SIGTERM <pid>
        $options = new DaemonOptions($sleep = 0.1, $timeout = 120, $limit = 10, $memory = 120);
        app(DaemonManager::class)->daemon($options, $processor);
    }
}
