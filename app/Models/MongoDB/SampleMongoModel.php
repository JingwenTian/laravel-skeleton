<?php
/**
 * Mongo 示例 Model.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2019/2/20 15:47
 */

namespace App\Models\MongoDB;

/**
 * Class SampleMongoModel.
 *
 * @package App\Models\MongoDB
 */
class SampleMongoModel extends AbstractMongoModel
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $collection = 'users';
}
