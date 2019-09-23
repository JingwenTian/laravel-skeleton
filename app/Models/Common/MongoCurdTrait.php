<?php
/**
 * MongoDB Model 通用CURD 方法.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/9/19 14:16
 */

namespace App\Models\Common;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

/**
 * Trait MongoCurdTrait.
 *
 * @package App\Models\Common
 */
trait MongoCurdTrait
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
                return $this->insertGetId($data);
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

            return $this->where($condition)->update($data);
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
     */
    public function read(array $condition = [], string $topic = ModelConsts::GET_ROW_TOPIC, array $fields = ['*'], array $relations = null)
    {
        try {
            // 调用方直接传入 a.b这样的二级检索条件, 在 laravel request 处理时会重置为 a_b (/api/item?user.name=jingwentian)
            // https://stackoverflow.com/questions/30886155/laravel-get-parameters-that-include-dots-cannot-be-checked
            // 所以针对 Mongo查询多级字段条件时, 以数组的方式传入, 如 ?content[org_id]=10035&content[user.user_id]=100
            $subConditions = [];
            foreach ($condition as $k => $v) {
                // 通过 GET 参数传入的多级字段参数
                if (is_array($v)) {
                    unset($condition[$k]);
                    foreach ($v as $subColumn => $subValue) {
                        // Mongo查询时是强类型校验, 在通过 GET 请求远程调用时, query 参数无法标识数据类型, 此处扩展类型转换
                        $subColumns = explode('|', $subColumn);
                        switch ($subColumns[1] ?? '') {
                            case 'string': $subValue = (string) $subValue; break;
                            case 'int': $subValue = (int) $subValue; break;
                            case 'bool': $subValue = (bool) $subValue; break;
                            case 'float': $subValue = (float) $subValue; break;
                        }
                        $subConditions[$k.'.'.$subColumns[0]] = $subValue;
                    }
                } elseif (Str::contains($k, '.')) { // 函数级调用时查询多级字段参数
                    unset($condition[$k]);
                    $subConditions[$k] = $subValue;
                }
            }
            /* @var \Illuminate\Database\Eloquent\Builder $query */
            $where = $condition;
            array_forget($where, ['_sort', '_order', '_group', '_offset', '_limit', '_page']);

            // 固定字段使用 Filter处理, 二级非结构化字段使用 Where
            $query = $this->filter($where)->where($subConditions);

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
