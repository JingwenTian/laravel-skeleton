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
 * Class ChinaPhoneValidator.
 *
 * @package App\Validators
 */
class ChinaPhoneValidator
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
        // 匹配所有支持短信功能的号码(手机卡 + 上网卡)
        // @see https://github.com/VincentSit/ChinaMobilePhoneNumberRegex
        return preg_match('/^(?:\+?86)?1(?:3\d{3}|5[^4\D]\d{2}|8\d{3}|7(?:[01356789]\d{2}|4(?:0\d|1[0-2]|9\d))|9[189]\d{2}|6[567]\d{2}|4[579]\d{2})\d{6}$/', $value);
    }
}
