<?php
/**
 * 手机号验证规则.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 21:08
 */

namespace App\Validators;

/**
 * Class PhoneValidator.
 *
 * @package App\Validators
 */
class PhoneValidator
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
        return preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|6[6]|7[0135678]|8[0-9])\d{8}$/', $value);
    }
}
