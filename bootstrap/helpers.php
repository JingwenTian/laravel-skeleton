<?php

use Illuminate\Support\Str;

if (!function_exists('sys_params')) {
    /**
     * 获取 Header 中的系统参数.
     *
     * @param string $key
     * @param string $needles
     *
     * @return array|mixed|string
     */
    function sys_params(string $key = '', $needles = 'x-gateway')
    {
        $key = Str::lower(trim($key));
        $needles = Str::endsWith($needles, '-') ? $needles : $needles.'-';
        $headers = request()->header();
        $sysItems = [];
        foreach ($headers as $name => $header) {
            if (Str::startsWith($name, $needles)) {
                $name = Str::lower(Str::replaceFirst($needles, '', $name));
                $sysItems[$name] = $header[0] ?? '';
            }
        }

        return $key === '' ? $sysItems : ($sysItems[$key] ?? '');
    }
}

if (!function_exists('pagination_format')) {
    /**
     * 分页数据格式化.
     *
     * @param $rows
     *
     * @return array
     */
    function pagination_format(array $rows)
    {
        return [
            'pagination'    => [
                'current_page'  => $rows['current_page'] ?? '',
                'from'          => $rows['from'] ?? null,
                'last_page'     => $rows['last_page'] ?? 0,
                'per_page'      => $rows['per_page'] ?? 15,
                'to'            => $rows['to'] ?? null,
                'total'         => $rows['total'] ?? 0,
            ],
            'data'  => $rows['data'] ?? [],
        ];
    }
}

if (!function_exists('lang')) {
    /**
     * 翻译字段(从Yaconf 配置服务中获取).
     *
     * @param string $key
     * @param string $default
     * @param array  $replace
     * @param string $locale
     *
     * @example lang('foo.bar')
     *          lang('foo.bar', 'default value', ['replace' => 'replace value'])
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function lang($key = null, $default = null, $replace = [], $locale = null)
    {
        $locale = $locale ?? app()->locale;
        $localeKey = 'lang_'.strtolower($locale ?: 'zh_CN');
        if (!$key) {
            return Yaconf::get($localeKey, []);
        }
        $localeVal = Yaconf::get($localeKey.'.'.$key, $default);
        if (is_string($localeVal) && $replace) {
            foreach ((array) $replace as $key => $value) {
                $localeVal = str_replace(
                    [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                    [$value, Str::upper($value), Str::ucfirst($value)],
                    $localeVal
                );
            }
        }

        return $localeVal;
    }
}

if (!function_exists('service_config')) {
    /**
     * 获取 yaconf 基础服务配置.
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    function service_config(string $key = null, string $default = null)
    {
        if (!$key) {
            return Yaconf::get('services', []);
        }

        return Yaconf::get('services.'.$key, $default);
    }
}
