<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'   => '',
        ],

        'mysql' => [
            'driver'         => 'mysql',
            'read'           => [
                'host'           => service_config('mysql.connect.read.host', '127.0.0.1'), //explode(',', env('DB_READ_HOST', '127.0.0.1')),
            ],
            'write' => [
                'host'           => service_config('mysql.connect.write.host', '127.0.0.1'), //env('DB_WRITE_HOST', '127.0.0.1'),
            ],
            'port'           => service_config('mysql.connect.port', '3306'), //env('DB_PORT', '3306'),
            'database'       => env('DB_DATABASE', 'forge'),
            'username'       => service_config('mysql.connect.username', 'forge'), //env('DB_USERNAME', 'forge'),
            'password'       => service_config('mysql.connect.password', ''), //env('DB_PASSWORD', ''),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => service_config('mysql.connect.charset', 'utf8mb4'),
            'collation'      => service_config('mysql.connect.collation', 'utf8mb4_unicode_ci'),
            'prefix'         => '',
            'strict'         => true,
            'engine'         => null,
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => service_config('mongo.connect.aliyun.host'), //explode(',', env('MONGO_DB_HOST')),
            'database' => env('MONGO_DB_DATABASE'),
            'username' => service_config('mongo.connect.aliyun.username'), //env('MONGO_DB_USERNAME'),
            'password' => service_config('mongo.connect.aliyun.password'), //env('MONGO_DB_PASSWORD'),
            'options'  => [
                'timeout'        => 300,
                'replicaSet'     => service_config('mongo.connect.aliyun.replicaset'), //env('MONGO_DB_REPLICA_SET'),
                'database'       => env('MONGO_DB_AUTHENTICATION_DATABASE'),
                'readPreference' => 'secondaryPreferred',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [
        'client' => 'predis',

        'default' => [
            'host'     => service_config('redis.connect.cluster.host'), //env('REDIS_HOST', '127.0.0.1'),
            'password' => service_config('redis.connect.cluster.password'), //env('REDIS_PASSWORD', null),
            'port'     => service_config('redis.connect.cluster.port'), //env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],
    ],
];
