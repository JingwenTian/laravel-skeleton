<?php
/**
 * 用户操作相关事务示例
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2019/2/20 15:49
 */

namespace App\Models\Trans;

use App\Exceptions\RuntimeException;
use App\Models\AbstractModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class UserTrans
 * @package App\Models\Trans
 */
class UserTrans extends AbstractModel
{
    /**
     * 数据库事务操作示例.
     *
     * @return array
     * @throws \Throwable
     */
    public function sampleUserTrans(): array
    {
        try {
            DB::beginTransaction();

            $mockUserItem = ['name' => 'hello' . time(), 'email' => microtime(true) . '@gmail.com', 'password' => password_hash(time(), 1)];
            throw_unless($userInserted = app(User::class)->create($mockUserItem), new RuntimeException('创建失败'));

            // 查询数据
            $condition = ['id' => $userInserted->id];
            throw_unless($userItem = app(User::class)->filter($condition)->first(), new RuntimeException('查询失败'));

            // 更新数据
            $userItem->name = 'hello world' . time();
            throw_unless($userItem->save(), new RuntimeException('修改失败'));

            return $userItem->toArray();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}