<?php
/**
 * 港澳通行证验证规则.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 21:09
 */

namespace App\Validators;

/**
 * Class HKCardValidator.
 *
 * @package App\Validators
 */
class HKCardValidator
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
        // 港澳居民来往内地通行证 http://ww1.sinaimg.cn/large/006tNc79ly1g4c1tvk093j30j709uahz.jpg
        // 规则： H/M + 10位或6位数字
        // 样本： H1234567890
        // /^[HMhm]{1}([0-9]{8})$/
        return preg_match('/^([A-Z]\d{6,10}(\(\w{1}\))?)$/', $value);
    }
}
