<?php
/**
 * 数字金额处理.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/12/13 11:28
 */

namespace App\Support\Normalize;

/**
 * Class Number.
 *
 * @package App\Support\Normalize
 */
class Number
{
    /**
     * 转换保留N位数的 Float值
     *
     * @param float $number
     * @param int   $limit  保留位数
     *
     * @return float
     */
    public static function float(float $number, int $limit = null): float
    {
        $limit = $limit ?? 2;

        return (float) \sprintf("%.{$limit}f", $number);
    }

    /**
     * 金额处理(用于计算).
     *
     * @param float    $price
     * @param int|null $limit
     *
     * @return float
     */
    public static function price(float $price, int $limit = null): float
    {
        return static::float($price >= 0 ? $price : 0, $limit ?? 2);
    }

    /**
     * 金额处理(用于显示).
     *
     * @param float $price
     *
     * @return string
     */
    public static function priceFormat(float $price): string
    {
        return \sprintf('%.2f', $price >= 0 ? $price : 0);
    }

    /**
     * 将数字格式化成人民币字符串.
     *
     * @param float $price
     *
     * @return string ￥100.00
     */
    public static function cny(float $price): string
    {
        setlocale(LC_MONETARY, 'zh_CN');

        return \money_format('%.2n', $price);
    }
}
