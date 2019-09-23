<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver'      => 'database',
            'table'       => 'jobs',
            'queue'       => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver'      => 'beanstalkd',
            'host'        => env('BEANSTALKD_HOST', '127.0.0.1'),
            'port'        => env('BEANSTALKD_PORT', 11300),
            'queue'       => 'default',
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key'    => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue'  => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
        ],

        // @doc https://learnku.com/docs/laravel/5.5/queues/1324
        // @doc https://redis.io/topics/cluster-spec#keys-hash-tags
        // 如果使用 Redis 集群, 队列名称必须包含key hash tag, 否则会抛错: ERR 'EVAL' command keys must in same slot
        'redis' => [
            'driver'      => 'redis',
            'connection'  => 'default',
            'queue'       => '{'.env('QUEUE_NAME', 'default').'}', // 队列名称: queues:{default}
            'retry_after' => 90,
        ],

        'kafka' => [
            'driver'      => 'kafka',
            'server'      => service_config('kafka.connnect.server', '127.0.0.1:9092'), //env('KAFKA_HOST', 'kafka-ons-internet.aliyun.com:8080'),
            //'username'    => env('KAFKA_USERNAME', 'xxxxxxx'),
            //'password'    => env('KAFKA_PASSWORD', 'xxxxxxx'),
            //'ssl_ca_path' => __DIR__.'/cert/kafka-ca-cert',
            'options'     => [
                // 生产者配置
                'publish'   => [
                    'test' => [
                        'topic'     => env('KAFKA_SUBSCRIBE_TEST_TOPIC', ''), // service_config('kafka.topic.xxxx.name', 'xxxxx'),
                    ],
                ],
                // 消费者配置
                'subscribe' => [
                    'test' => [
                        'topic'     => env('KAFKA_SUBSCRIBE_TEST_TOPIC', ''),
                        'consumer'  => env('KAFKA_SUBSCRIBE_TEST_CONSUMER', ''), // service_config('kafka.consumer.xxxx.name', 'xxxx'),
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    | @doc https://learnku.com/docs/laravel/5.5/queues/1324
    |
    | 使用 redis 作为队列时, 任务超出重试次数后会插入 failed_jobs 表中失败的消息, 待人工介入处理
    | 1. 如果使用队列必须要创建 failed_jobs 表(无法将失败的消息存入redis)
    | 2. 失败的消息无法自动处理(超过了自动重试次数), 需要手工进行重试
    | 3. 相关命令
    |   3.1. 创建failed_jobs表:
    |       php artisan queue:failed-table
    |       php artisan migrate
    |   3.2. 列出需要重试的消息: php artisan queue:failed
    |   3.3. 执行重试: php artisan queue:retry [id|all]
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => 'failed_jobs',
    ],
];
