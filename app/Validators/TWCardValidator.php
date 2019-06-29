<?php
/**
 * 台胞证验证规则.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 21:09
 */

namespace App\Validators;

/**
 * Class TWCardValidator.
 *
 * @package App\Validators
 */
class TWCardValidator
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
        // 台湾居民来往大陆通行证 http://ww1.sinaimg.cn/large/006tNc79ly1g4c1uipn24j30j709u464.jpg
        // [2002版] http://ww4.sinaimg.cn/large/006tNc79ly1g4c1jq4ls0j30i20g8go1.jpg
        // [2015版] http://ww4.sinaimg.cn/large/006tNc79ly1g4c1kwhpvwj30i20g8go2.jpg
        return preg_match('/^[0-9]{8}$/', $value);
    }
}
