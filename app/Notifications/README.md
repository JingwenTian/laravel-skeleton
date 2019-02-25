# 消息通知

参考文档: https://learnku.com/docs/laravel/5.7/notifications/2284

## i. 创建通知

> Laravel 中一条通知就是一个类 (通常存放在 app/Notifications 文件夹里)。看不到的话不要担心，运行下 make:notification 命令就能创建了：

```
php artisan make:notification SampleNotification
```

## ii. 通知队列

> 使用通知队列前需要配置队列并 开启一个队列任务

发送通知可能是耗时的，尤其是通道需要调用额外的 API 来传输通知。为了加速应用的响应时间，可以将通知推送到队列中异步发送，而要实现推送通知到队列，可以让对应通知类实现 ShouldQueue 接口并使用 Queueable trait 

```
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SampleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // ...
}
```

## iii. 邮件通知

### 格式化邮件消息

```
/**
 * Get the mail representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */
public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('测试消息通知')  // 邮件标题
        ->greeting($this->user->name.'您好：') // 欢迎词
        ->line('您于 '.$this->user->created_at->format('m-d H:i').' 注册账户成功。') // 邮件内容
        ->action('查看详情', 'http://baidu.com') // 邮件中的按钮及对应链接
        ->success(); // 按钮的色调
}
```

### 发送通知

```
Notification::route('mail', '562234934@qq.com')->notify(new SampleNotification($userItem));
```