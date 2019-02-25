<?php

namespace App\Providers;

use App\Validators\IdNumberValidator;
use App\Validators\PhoneValidator;
use ELog\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $validators = [
        'phone'     => PhoneValidator::class,
        'id_no'     => IdNumberValidator::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SQL 日志
        DB::listen(function ($event) {
            $params = [
                'sql'      => $event->sql,
                'bindings' => $event->bindings,
                'time'     => $event->time,
            ];
            app()->elog->info(Constants::TOPIC_SQL, 'sqllog:'.$event->connectionName, $params);
        });
        // 注册 Validator
        $this->registerValidators();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * 注册 Validator.
     */
    protected function registerValidators(): void
    {
        foreach ($this->validators as $rule => $validator) {
            Validator::extend($rule, "{$validator}@validate");
        }
    }
}
