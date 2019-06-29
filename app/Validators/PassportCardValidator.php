<?php
/**
 * 护照验证规则.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 21:09
 */

namespace App\Validators;

/**
 * Class PassportCardValidator.
 *
 * @package App\Validators
 */
class PassportCardValidator
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return false|int
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        // 规则： 14/15开头 + 7位数字, G + 8位数字, P + 7位数字, S/D + 7或8位数字,等
        // 样本： 141234567, G12345678, P1234567
        // /^[a-zA-Z]{5,17}$/ 或 /^[a-zA-Z0-9]{5,17}$/
        return preg_match('/^([a-zA-z]|[0-9]){5,17}$/', $value);
    }
}
