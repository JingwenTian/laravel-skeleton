<?php
/**
 * Model基类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/17 16:23
 */

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Common\CurdTrait;

/**
 * Class AbstractModel.
 *
 * @package App\Models
 */
abstract class AbstractModel extends Model
{
    use SoftDeletes, Filterable, LogsActivity, CurdTrait;

    /**
     * 该模型是否被自动维护时间戳.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    public const CREATED_AT = 'created_at';
    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    public const UPDATED_AT = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | 扩展方法
    |--------------------------------------------------------------------------
    */

    /**
     * 获取数据库可以批量添加字段值数组.
     *
     * @return array
     */
    public function getFillableFields(): array
    {
        return $this->fillable;
    }

    /*
    |--------------------------------------------------------------------------
    | Activity log: Logging model events
    |--------------------------------------------------------------------------
    | Model自动记录更新类操作的修改记录
    | @see https://github.com/spatie/laravel-activitylog
    */

    /**
     * [Activity log] 仅记录差异数据.
     *
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * [Activity log] 记录操作的行为.
     *
     * @var array
     */
    protected static $recordEvents = ['created', 'updated', 'deleted'];

    /**
     * [Activity log] 记录的名称标识.
     *
     * @var string
     */
    protected static $logName = 'system.model';
}
