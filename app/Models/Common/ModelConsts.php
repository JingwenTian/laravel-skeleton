<?php
/**
 * Model 常量配置.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/19 18:33
 */

namespace App\Models\Common;

/**
 * Class ModelConsts.
 *
 * @package App\Models\Common
 */
class ModelConsts
{
    /*
    |--------------------------------------------------------------------------
    | Common Config
    |--------------------------------------------------------------------------
    | 通用常量配置
    |
    */

    // 数据获取姿势
    public const GET_ROW_TOPIC = 'row'; // 获取单条记录
    public const GET_ALL_TOPIC = 'all'; // 获取所有记录
    public const GET_COUNT_TOPIC = 'count'; // 获取记录行数
    public const GET_PAGE_TOPIC = 'page'; // 手工获取分页记录
    public const GET_PAGINATE_TOPIC = 'paginate'; // 自动获取分页记录
    public const GET_MAX_TOPIC = 'max'; // 获取最大值
    public const GET_MIN_TOPIC = 'min'; // 获取最小值
    public const GET_AVG_TOPIC = 'avg'; // 获取平均数
    public const GET_SUM_TOPIC = 'sum'; // 获取数据和
    public const GET_HAS_TOPIC = 'has'; // 是否存在某数据

    public const PAGE_SIZE = 10; // 默认分页配置
    public const PAGE_MAX_SIZE = 2000; // 分页最大值
}
