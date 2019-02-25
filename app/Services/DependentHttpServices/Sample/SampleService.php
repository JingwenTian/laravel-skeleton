<?php

/**
 * HTTP 请求示例.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/27 13:59
 */

namespace App\Services\DependentHttpServices\Sample;

use App\Exceptions\RuntimeException;
use App\Services\DependentHttpServices\AbstractBaseService;
use App\Support\Constant\CacheKey;
use Illuminate\Support\Facades\Cache;

/**
 * Class SampleService.
 *
 * @package App\Services\DependentHttpServices
 */
class SampleService extends AbstractBaseService
{
    /**
     * @var string
     */
    protected $server = 'dependence.client.sample';

    /**
     * @var string
     */
    protected $path = 'public';

    /**
     * 请求示例1: 获取省份列表.
     *
     * @throws \Throwable
     *
     * @return array
     */
    public function provinceItems(): array
    {
        $resource = $this->call(self::HTTP_METHOD_GET, 'city', [], [], []);

        throw_if($this->hasError(), new RuntimeException($resource['message'], $resource['code']));

        return $resource['data'] ?? [];
    }

    /**
     * 请求示例2: 获取手机号国别代码.
     *
     * @throws \Throwable
     *
     * @return array
     */
    public function countryCodeItems(): array
    {
        // 应用缓存示例.
        // 1. 需要单独维护缓存KEY
        // 2. 指定过期时间
        $resource = Cache::remember(CacheKey::DATA_ITEMS, 60, function () {
            return $this->call(self::HTTP_METHOD_GET, 'countrycode', [], [], []);
        });

        throw_if($this->hasError(), new RuntimeException($resource['message'], $resource['code']));

        return $resource['data'] ?? [];
    }
}
