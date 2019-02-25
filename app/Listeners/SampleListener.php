<?php

namespace App\Listeners;

use App\Events\SampleEvent;
use App\Notifications\SampleNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SampleListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param SampleEvent $event
     */
    public function handle(SampleEvent $event)
    {
        // 信息通知
        $user = $event->getUser();

        //app()->elog->info('listeners', 'sample listener', [time()]);

        Notification::route('mail', '562234934@qq.com')->notify(new SampleNotification($user));
    }
}
