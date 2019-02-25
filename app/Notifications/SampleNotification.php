<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SampleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //app()->elog->info('notification', 'send email', $this->user->toArray());

        return (new MailMessage())
            ->subject('测试消息通知')  // 邮件标题
            ->greeting($this->user->name.'您好：') // 欢迎词
            ->line('您于 '.$this->user->created_at->format('m-d H:i').' 注册账户成功。') // 邮件内容
            ->action('查看详情', 'http://baidu.com') // 邮件中的按钮及对应链接
            ->success(); // 按钮的色调
    }
}
