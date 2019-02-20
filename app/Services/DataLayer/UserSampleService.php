<?php
/**
 * 用户数据服务示例.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2019/2/20 16:13
 */

namespace App\Services\DataLayer;

use App\Models\Common\ModelConsts;
use App\Models\User;
use App\Support\Constant\CacheKey;
use Illuminate\Support\Facades\Cache;

/**
 * Class UserSampleService
 * @package App\Services\DataLayer
 */
class UserSampleService
{
    /**
     * 获取用户详情.
     *
     * @param int $userId
     * @return array
     */
    public static function userItem(int $userId): array
    {
        return Cache::remember(CacheKey::USER_ITEM.$userId, 60, function () use ($userId) {
            return app(User::class)->read(['id' => $userId]);
        });
    }

    /**
     * 获取用户列表.
     *
     * @param array $params
     * @return array
     */
    public static function userItems(array $params): array
    {
        return Cache::remember(CacheKey::USER_ITEMS.json_encode($params), 60, function () use ($params) {
            return app(User::class)->read($params, ModelConsts::GET_ALL_TOPIC);
        });
    }

}