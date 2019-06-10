<?php
/**
 * 获取本地化语言项.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 11:05
 */

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Log\LogServiceProvider as BaseLogServiceProvider;

/**
 * Class LocalizationProvider.
 *
 * @see https://learnku.com/docs/laravel/5.5/localization/1305
 * @see https://github.com/caouecs/Laravel-lang
 * @see https://github.com/ARCANEDEV/LaravelLang
 * @see https://github.com/overtrue/laravel-lang
 * @see https://github.com/ablunier/laravel-lang-installer
 * @see https://github.com/andrey-helldar/laravel-lang-publisher
 *
 * @example 注册 Providers: config/app.php -> 'providers' => [App\Providers\LocalizationProvider::class]
 *
 * @package App\Providers
 */
class LocalizationProvider extends BaseLogServiceProvider
{
    const FROM_URI_PATH = 1;
    const FROM_URI_PARAM = 2;
    const FROM_COOKIE = 3;
    const FROM_HEADER = 4;

    protected $availableLocales;
    protected $defaultLocale;

    protected $searchOrder;

    protected $uriParamName;
    protected $cookieName;

    /**
     * Register the service provider.
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $locales = config('app.available_locales'); // 支持的所有语言.
        $default = config('app.locale', 'zh_CN'); // 默认指定的语言.
        $this->setAvailableLocales($locales);
        $this->setDefaultLocale($default);
        // 获取前端指定的语言标识
        $this->setSearchOrder(
            [self::FROM_URI_PATH, self::FROM_URI_PARAM, self::FROM_COOKIE, self::FROM_HEADER]
        );
        $this->setUriParamName('locale');
        $this->setCookieName('locale');
        $locale = $this->getLocale(app()->request);
        app()->setLocale($locale);
    }

    /**
     * @param array $locales a list of available locales
     */
    public function setAvailableLocales(array $locales)
    {
        $this->availableLocales = [];
        foreach ($locales as $locale) {
            $this->availableLocales[] = $this->parseLocale($locale);
        }
    }

    /**
     * @param string $default the default locale
     */
    public function setDefaultLocale(string $default)
    {
        $this->defaultLocale = $default;
    }

    /**
     * @param array $order the order in which the search will be performed to
     *                     resolve the locale
     */
    public function setSearchOrder(array $order)
    {
        $this->searchOrder = $order;
    }

    /**
     * @param string $name the name for the locale URI parameter
     */
    public function setUriParamName(string $name)
    {
        $this->uriParamName = $name;
    }

    /**
     * @param string $name the name for the locale cookie
     */
    public function setCookieName(string $name)
    {
        $this->cookieName = $name;
    }

    /**
     * Get current locale.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getLocale(Request $request): string
    {
        foreach ($this->searchOrder as $order) {
            switch ($order) {
                case self::FROM_URI_PATH:
                    $locale = $this->localeFromPath($request);
                    break;
                case self::FROM_URI_PARAM:
                    $locale = $this->localeFromParam($request);
                    break;
                case self::FROM_COOKIE:
                    $locale = $this->localeFromCookie($request);
                    break;
                case self::FROM_HEADER:
                    $locale = $this->localeFromHeader($request);
                    break;
                default:
                    throw new \DomainException('Unknown search option provided');
            }
            if (!empty($locale)) {
                return $locale;
            }
        }

        return $this->defaultLocale;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function localeFromPath(Request $request): string
    {
        list(, $value) = explode('/', $request->path());

        return $this->filterLocale($value);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function localeFromParam(Request $request): string
    {
        $params = $request->all();
        $value = isset($params[$this->uriParamName]) ? $params[$this->uriParamName] : '';

        return $this->filterLocale($value);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function localeFromCookie(Request $request): string
    {
        // 先取 cookie, 再合并上网关转发的 cookie
        $cookies = $request->cookie() + $this->cookieFromGateway();
        $value = isset($cookies[$this->cookieName]) ? $cookies[$this->cookieName] : '';

        return $this->filterLocale($value);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function localeFromHeader(Request $request): string
    {
        // https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers/Accept-Language
        // Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,zh-TW;q=0.7,es;q=0.6,fr;q=0.5,ja;q=0.4,ko;q=0.3,pt;q=0.2,ru;q=0.1,de;q=0.1,it;q=0.1

        // 兼容 RPC 调用时Accept-Language 为空时的问题
        $language = $request->header('Accept-Language') ?: 'zh-CN,zh;q=0.9';
        $values = $this->parse($language);
        //$values = $this->parse($request->getHeaderLine('Accept-Language'));
        usort($values, [$this, 'sort']);
        foreach ($values as $value) {
            $value = $this->filterLocale($value['locale']);
            if (!empty($value)) {
                return $value;
            }
        }
        // search language if a full locale is not found
        foreach ($values as $value) {
            $value = $this->filterLocale($value['language']);
            if (!empty($value)) {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    protected function filterLocale(string $locale): string
    {
        // return the locale if it is available
        foreach ($this->availableLocales as $avail) {
            if ($locale == $avail['locale']) {
                return $locale;
            }
        }

        return '';
    }

    /**
     * @param string $header
     *
     * @return array
     */
    protected function parse(string $header): array
    {
        // the value may contain multiple languages separated by commas,
        // possibly as locales (ex: en_US) with quality (ex: en_US;q=0.5)
        $values = [];
        foreach (explode(',', $header) as $lang) {
            @list($locale, $quality) = explode(';', $lang, 2);
            $val = $this->parseLocale($locale);
            $val['quality'] = $this->parseQuality(isset($quality) ? $quality : '');
            $values[] = $val;
        }

        return $values;
    }

    /**
     * @param string $locale
     */
    protected function parseLocale(string $locale)
    {
        // Locale format: language[_territory[.encoding[@modifier]]]
        //
        // Language and territory should be separated by an underscore
        // although sometimes a hyphen is used. The language code should
        // be lowercase. Territory should be uppercase. Take this into
        // account but normalize the returned string as lowercase,
        // underscore, uppercase.
        //
        // The possible codeset and modifier is discarded since the header
        // *should* really list languages (not locales) in the first place
        // and the chances of needing to present content at that level of
        // granularity are pretty slim.
        $lang = '([[:alpha:]]{2})';
        $terr = '([[:alpha:]]{2})';
        $code = '([-\\w]+)';
        $mod = '([-\\w]+)';
        $regex = "/$lang(?:[-_]$terr(?:\\.$code(?:@$mod)?)?)?/";
        preg_match_all($regex, $locale, $m);
        $locale = $language = strtolower($m[1][0]);
        if (!empty($m[2][0])) {
            $locale .= '_'.strtoupper($m[2][0]);
        }

        return [
            'locale'   => $locale,
            'language' => $language,
        ];
    }

    protected function parseQuality(string $quality)
    {
        // If no quality is given then return 0.00001 as a sufficiently
        // small value for sorting purposes.
        @list(, $value) = explode('=', $quality, 2);

        return (float) ($value ?: 0.0001);
    }

    protected function sort(array $a, array $b)
    {
        // Sort order is determined first by quality (higher values are
        // placed first) then by order of their apperance in the header.
        if ($a['quality'] < $b['quality']) {
            return 1;
        }
        if ($a['quality'] == $b['quality']) {
            return 0;
        }

        return -1;
    }

    /**
     * 从网关中解析COOKIE.
     *
     * @param string $key
     * @param [type] $default
     *
     * @return mixed
     */
    protected function cookieFromGateway(string $key = null, $default = null)
    {
        $cookieString = sys_params('http-cookie');
        if (!$cookieString) {
            return [];
        }
        $cookies = [];
        foreach (explode(';', $cookieString) as $cookieItem) {
            if (!$cookieItem || strpos($cookieItem, '=') === false) {
                continue;
            }
            [$cookieName, $cookieValue] = explode('=', $cookieItem);
            $cookies[trim($cookieName)] = trim($cookieValue);
        }

        return $key !== null ? ($cookies[$key] ?? $default) : ($cookies ?? $default);
    }
}
