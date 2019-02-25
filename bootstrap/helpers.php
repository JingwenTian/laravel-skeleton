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
