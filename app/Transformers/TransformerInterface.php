<?php
/**
 * 数据格式统一化处理接口类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/19 19:53
 */

namespace App\Transformers;

/**
 * Interface TransformerInterface.
 *
 * @package App\Transformers
 */
interface TransformerInterface
{
    public function transform(array $data);
}
