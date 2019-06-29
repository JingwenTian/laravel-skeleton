<?php
/**
 * 快递单号验证规则.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/24 14:09
 */

namespace App\Validators;

/**
 * Class ExpressNoValidator.
 *
 * @package App\Validators
 */
class ExpressNoValidator
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return false|int
     *
     * @see https://www.kuaidi100.com/
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return preg_match('/^[0-9a-zA-Z]{4,}$/', $value);
    }
}
