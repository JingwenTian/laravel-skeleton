<?php
/**
 * Model 通用CURD 方法.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/19 18:32
 */

namespace App\Models\Common;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * trait CurdTrait.
 *
 * @package App\Models\Common
 */
trait CurdTrait
{
    /**
     * 创建记录.
     *
     * @param array $data
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function add(array $data = null)
    {
        try {
            if (!$data) {
                return false;
            }
            if (array_keys($data) !== array_keys(array_keys($data))) {
                //return $this->insertGetId($data);
                return $this->create($data);
            }

            return $this->insert($data);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * 更新记录.
     *
     * @param array $condition
     * @param array $data
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function edit(array $condition = null, array $data = null)
    {
        try {
            if (!$condition || !$data) {
                return false;
            }

            return $this->filter($condition)->update($data);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * 查询记录.
     *
     * 支持: 单条、多条、分页、数量、最大值、最小值、平均数、求和
     *
     * @doc 扩展 Query 参数:
     * | Filed      | Desc
     * |------------|-------------
     * | _page      | 当前页数
     * | _limit     | 一页显示条数
     * | _sort      | 排序依照(create_date)
     * | _order     | 排序顺序(desc)
     *
     * @param array  $condition 查询条件
     * @param string $topic     查询类型
     * @param array  $fields    查询字段
     * @param array  $relations 关联模型
     *
     * @throws \Throwable
     *
     * @return mixed
     * @return mixed
     */
    public function read(array $condition = [], string $topic = ModelConsts::GET_ROW_TOPIC, array $fields = ['*'], array $relations = null)
    {
        try {
            /* @var \Illuminate\Database\Eloquent\Builder $query */
            $query = $this->filter($condition);
            // Relations process
            if ($relations) {
                $query->with($relations);
            }

            // OrderBy process
            if ($orderBy = array_get($condition, '_sort')) {
                $query->orderBy($orderBy, array_get($condition, '_order', 'desc'));
            } else {
                $query->orderBy($this->primaryKey, 'desc');
            }

            // GroupBy process
            if ($groupBy = array_get($condition, '_group')) {
                $query->groupBy($groupBy);
            }

            // Paginate params process
            $offset = (int) array_get($condition, '_offset', 0);
            $limit = (int) array_get($condition, '_limit', ModelConsts::PAGE_SIZE);
            $limit = ($limit > ModelConsts::PAGE_MAX_SIZE) ? ModelConsts::PAGE_MAX_SIZE : $limit;

            // Fields Process
            $allFields = array_unique(array_merge([$this->primaryKey], $this->fillable, ['created_at', 'updated_at']));
            $fields = array_intersect($fields ?: ['*'], $allFields) ?: $allFields;

            switch ($topic) {
                default:
                case ModelConsts::GET_ROW_TOPIC:
                    return $query->select($fields)->firstOrFail()->toArray();
                    break;
                case ModelConsts::GET_ALL_TOPIC:
                    return $query->select($fields)->get()->toArray();
                    break;
                case ModelConsts::GET_PAGE_TOPIC:
                    return $query->select($fields)->offset($offset)->limit($limit)->get()->toArray();
                    break;
                case ModelConsts::GET_PAGINATE_TOPIC:
                    $rows = $query->paginate($limit, $fields, '_page')->toArray();

                    return pagination_format($rows);
                    break;
                case ModelConsts::GET_COUNT_TOPIC:
                case ModelConsts::GET_MAX_TOPIC:
                case ModelConsts::GET_MIN_TOPIC:
                case ModelConsts::GET_AVG_TOPIC:
                case ModelConsts::GET_SUM_TOPIC:
                    return $query->{$topic}($fields[0] ?? 'id');
                    break;
            }

            return [];
        } catch (ModelNotFoundException $e) {
            return [];
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
