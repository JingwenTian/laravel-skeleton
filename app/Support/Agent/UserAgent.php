<?php
/**
 * User Agent.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/4/16 12:16
 */

namespace App\Support\Agent;

use Jenssegers\Agent\Agent;

/**
 * Class UserAgent.
 *
 * @package App\Support\Agent
 */
class UserAgent extends Agent
{
    protected static $beforeBrowsers = [
        'WeChat' => 'MicroMessenger',
    ];

    protected static $beforeProperties = [
        'WeChat' => 'MicroMessenger/[VER]',
    ];

    public function __construct(array $headers = null, $userAgent = null)
    {
        static::$additionalProperties = array_merge(static::$beforeProperties, static::$additionalProperties);
        static::$additionalBrowsers = array_merge(static::$beforeBrowsers, static::$additionalBrowsers);
        parent::__construct($headers, $userAgent);
    }

    public function setUserAgent($userAgent = null): string
    {
        if ($userAgent === null) {
            $userAgent = sys_params('client-user-agent');
        }

        return parent::setUserAgent($userAgent);
    }

    /**
     * 是否在微信环境.
     *
     * @return bool
     */
    public function isWeChat(): bool
    {
        $userAgent = $this->getUserAgent();
        if (strpos($userAgent, 'MicroMessenger') === false) {
            return false;
        }

        return true;
    }

    /**
     * 是否在微信小程序环境.
     *
     * ps. 小程序内的 webview 拿到的 user-agent 中包含微信
     *
     * @return bool
     */
    public function isMiniProgram(): bool
    {
        $userAgent = $this->getUserAgent();
        if (strpos($userAgent, 'miniProgram') !== false || strpos($userAgent, 'miniprogram') !== false) {
            return true;
        }

        return false;
    }

    public function version($propertyName, $type = Agent::VERSION_TYPE_STRING): string
    {
        $version = Agent::version($propertyName, $type);
        if ($version) {
            $version = str_replace('_', '.', $version);
        } else {
            $version = '';
        }

        return $version;
    }

    protected function findDetectionRulesAgainstUA(array $rules, $userAgent = null): string
    {
        $res = parent::findDetectionRulesAgainstUA($rules, $userAgent);
        if (!$res) {
            $res = '';
        }

        return $res;
    }
}
