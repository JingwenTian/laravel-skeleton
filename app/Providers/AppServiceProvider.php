<?php

namespace App\Providers;

use App\Validators\ChinaPhoneValidator;
use App\Validators\ExpressNoValidator;
use App\Validators\HKCardValidator;
use App\Validators\IdCardValidator;
use App\Validators\PassportCardValidator;
use App\Validators\TWCardValidator;
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
        'express_no'     => ExpressNoValidator::class,
        'chaina_phone'   => ChinaPhoneValidator::class,
        'id_card'        => IdCardValidator::class,
        'hk_card'        => HKCardValidator::class,
        'passport_card'  => PassportCardValidator::class,
        'tw_card'        => TWCardValidator::class,
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
