<?php
/**
 * EloquentFilter 条件处理基类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 10:44
 */

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

/**
 * Class AbstractFilters.
 *
 * @package App\ModelFilters
 */
abstract class AbstractFilters extends ModelFilter
{
    /**
     * Drop `_id` from the end of input keys when referencing methods.
     *
     * @var bool
     */
    protected $drop_id = false;

    /**
     * Filter global methods.
     */
    public function setup()
    {
        if ($this->input('with_trashed') !== null) { // 包括软删除
            $this->withTrashed();
        }
        if ($this->input('only_trashed') !== null) { // 只查删除
            $this->onlyTrashed();
        }
    }
}
