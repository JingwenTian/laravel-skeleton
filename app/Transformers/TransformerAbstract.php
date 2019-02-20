<?php
/**
 * 数据格式统一化处理继承类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/19 19:54
 */

namespace App\Transformers;

use League\Fractal\TransformerAbstract as TransformerBaseAbstract;

/**
 * Class TransformerAbstract.
 *
 * @package App\Transformers
 */
abstract class TransformerAbstract extends TransformerBaseAbstract implements TransformerInterface
{
    /**
     * @var array
     */
    protected $_queries;

    /**
     * TransformerAbstract constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        $this->_queries = $params ?? [];
    }

    public function transform(array $data = null)
    {
        return $this->transformData($data ?? []);
    }

    abstract public function transformData(array $data);
}
