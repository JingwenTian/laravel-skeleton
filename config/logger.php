<?php

use ELog\Constants;

return [
    /*
   |--------------------------------------------------------------------------
   | ELog Service Configures ELK 日志服务配置
   |--------------------------------------------------------------------------
   |
   */
    'logger_options' => [
        'log_level'     => config('app.env') === 'production' ? \Monolog\Logger::INFO : \Monolog\Logger::DEBUG,
        'log_path'      => config('app.env') === 'production' ? Constants::ELOG_PATH : storage_path('logs'),
        'send_email'    => true,
        'email_options' => [
            'subject' => '活动易权限服务报警'.config('app.env'),
            'send_to' => ['order-alarm@eventmosh.com' => '活动易订单财务交易服务报警'],
        ],
    ],
];
