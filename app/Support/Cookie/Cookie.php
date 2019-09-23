<?php
/**
 * 网关转发 Cookie 处理.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/10/16 17:07
 */

namespace App\Support\Cookie;

/**
 * Class Cookie.
 *
 * @package App\Support\Cookie
 */
class Cookie
{
    /**
     * @var string
     */
    private $cookieString;

    /**
     * @var array
     */
    private $cookieArray;

    /**
     * Cookie constructor.
     *
     * @param string|null $cookies
     */
    public function __construct(string $cookies = null)
    {
        $this->setCookie($cookies);
    }

    /**
     * @param string|null $cookies
     */
    public function setCookie(string $cookies = null): void
    {
        $this->cookieString = $cookies ?: sys_params('http-cookie');
    }

    /**
     * @param string|null $default
     * @param string|null $key
     *
     * @return array|mixed|string
     */
    public function getCookie(string $key = null, $default = null)
    {
        $cookies = $this->toArray();

        return $key !== null ? ($cookies[$key] ?? $default) : ($cookies ?? $default);
    }

    /**
     * @return array
     */
    private function toArray(): array
    {
        if ($this->cookieString === null) {
            return [];
        }
        $stringToArray = explode(';', $this->cookieString);
        foreach ($stringToArray as $cookieItem) {
            if (!$cookieItem || strpos($cookieItem, '=') === false) {
                continue;
            }
            [$cookieName, $cookieValue] = explode('=', $cookieItem);
            $this->cookieArray[trim($cookieName)] = trim($cookieValue);
        }

        return $this->cookieArray ?? [];
    }
}
