<?php
/**
 * MongoDB Model基类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/9/19 12:17
 */

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class AbstractMongoModel.
 *
 * @package App\Models
 */
abstract class AbstractMongoModel extends Model
{
    use HybridRelations;

    /**
     * 该模型是否被自动维护时间戳.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    public const CREATED_AT = 'create_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    public const UPDATED_AT = 'update_date';
}
