<?php

namespace App\Foundation;

use App\Providers\LogServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Routing\RoutingServiceProvider;

/**
 * Class Application.
 *
 * @package App\Foundation
 */
class Application extends BaseApplication
{
    /**
     * Register all of the base service providers.
     */
    protected function registerBaseServiceProviders(): void
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this)); // 覆盖框架注册日志组件逻辑
        $this->register(new RoutingServiceProvider($this));
    }
}
