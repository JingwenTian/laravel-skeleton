<?php

namespace App\Repositories;

use App\Exceptions\RuntimeException;
use App\Models\Common\ModelConsts;
use App\Models\Trans\UserTrans;
use App\Models\User;
use App\Services\DependentHttpServices\Sample\SampleService;
use App\Models\MongoDB\SampleMongoModel;

/**
 * Class SampleRepository.
 *
 * @package App\Repositories
 */
class SampleRepository
{

    /**
     * SampleRepository constructor.
     */
    public function __construct()
    {
    }

    /**
     * HTTP服务依赖请求示例.
     *
     * @return array
     */
    public function httpDependenceSample(): array
    {
        $provinceItems = app(SampleService::class)->provinceItems();
        $codeItems = app(SampleService::class)->countryCodeItems();

        return [
            'provinces' => $provinceItems,
            'country_code' => $codeItems,
        ];
    }

    /**
     * MySQL数据操作示例.
     *
     * @return array
     * @throws \Throwable
     */
    public function mysqlSample(): array
    {
        // 新增数据
        $mockUserItem = ['name' => 'hello' . time(), 'email' => time() . '@gmail.com', 'password' => password_hash(time(), 1)];
        throw_unless($userInserted = app(User::class)->create($mockUserItem), new RuntimeException('创建失败'));

        // 查询数据
        $condition = ['id' => $userInserted->id];
        throw_unless($userItem = app(User::class)->filter($condition)->first(), new RuntimeException('查询失败'));

        // 更新数据
        //app(User::class)->filter($condition)->update(['name' => 'hello world' . time()]);
        $userItem->name = 'hello world' . time();
        throw_unless($userItem->save(), new RuntimeException('修改失败'));
        //return $userItem->toArray();

        // 事务操作示例
        throw_unless($userTrans = app(UserTrans::class)->sampleUserTrans(), new RuntimeException('事务失败'));
        //return $userTrans->toArray();

        // 分页查询
        return app(User::class)->read(['id_gte' => 1], ModelConsts::GET_PAGINATE_TOPIC);
    }

    /**
     * MongoDB 数据操作示例.
     *
     * @return array
     * @throws \Throwable
     */
    public function mongoSample(): array
    {
        // 新增数据
        $mockItem = ['name' => 'hello', 'email' => 'hello@gmail.com', 'age' => random_int(1, 100), 'tags' => 'sample'];
        app(SampleMongoModel::class)->insert($mockItem);

        // 查询数据
        $condition = ['tags' => 'sample', '_sort' => '_id', '_order' => 'asc'];
        return app(SampleMongoModel::class)->read($condition, ModelConsts::GET_ALL_TOPIC);
    }

    public function kafkaSample(): array
    {

    }

}
