<?php
/**
 * 日志 Provider.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 11:05
 */

namespace App\Providers;

use ELog\Adapters\FileLogger;
use ELog\ELogClient;
use Illuminate\Log\LogServiceProvider as BaseLogServiceProvider;

class LogServiceProvider extends BaseLogServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();
        // register elog
        $this->app->singleton('elog', function () {
            return new ELogClient(new FileLogger());
        });
    }
}
