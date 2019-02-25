# 队列系统

文档参考: https://learnku.com/docs/laravel/5.7/queues/2286

## i. 创建任务

> 队列的任务类都默认放在 app/Jobs 目录下, 如果这个目录不存在，那当你运行 make:job

```bash
php artisan make:job SampleJobs
```

## ii. 分发任务

> 如果你想延迟你的队列任务的执行，你可以在分发任务的时候使用 delay 方法。例如，十秒之后才会执行的任务

```php
<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\SampleJobs;

class SampleController extends Controller
{
    public function sample(Request $request)
    {
        $userItem = app(User::class)->filter(['id' => 1])->first();
        
        // 分发异步队列任务
        SampleJobs::dispatch($userItem);
        
        // 延时分发
        SampleJobs::dispatch($userItem)->delay(now()->addSeconds(10));
    }
}
```

## iii. 运行队列处理器

> Laravel 包含了一个队列处理器以将推送到队列中的任务执行。你可以使用 queue:work Artisan 命令运行处理器。 注意一旦 queue:work 命令开始执行，它会一直运行直到它被手动停止或终端被关闭。

```bash
php artisan queue:work

php artisan queue:work --sleep=3 --tries=3 --timeout=120
# --sleep 选项定义了如果没有新任务的时候处理器将会「睡眠」多长时间
# --tries 选项定义了在一个任务重指定最大尝试次数
# --timeout 选项定义了任务执行最大秒数的数值
```

## iv. Supervisor 配置

> Supervisor 是一个 Linux 下的进程管理器，它会在 queue:work 进程关闭后自动重启
>
> Supervisord配置统一维护在: [eventmosh/basic-service-resource](https://github.com/eventmosh/basic-service-resource)

```
; 订单任务分发
[program:order-laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=/home/pubsrv/php-7.1.10/bin/php /home/mosh/3.0/em-order/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=mosh
numprocs=2
redirect_stderr=true
stdout_logfile=/home/mosh/pub/logs/supervisord/output/order-laravel-worker.log
```
