<?php
/**
 * Yar RPC请求示例.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/27 13:59
 */

namespace App\Services\DependentRpcServices\Sample;

use App\Exceptions\RuntimeException;
use App\Services\DependentRpcServices\AbstractBaseService;
use App\Support\Status\Status;

/**
 * Class SampleService
 * @package App\Services\DependentRpcServices\Account
 */
class SampleService extends AbstractBaseService
{
    /**
     * @var string
     */
    protected $server = 'dependence.service.sample';
    /**
     * @var string
     */
    protected $path = 'sample';

    /**
     * 调用示例.
     *
     * @param int $userId
     * @return array
     * @throws \Throwable
     */
    public function getSample(int $userId): array
    {
        $resource = $this->call('getUserInfo', $userId);

        throw_if(
            $this->hasError(),
            new RuntimeException($this->getError(), Status::CODE_COMMON_RESOURCE_NOT_EXIST)
        );
        return $resource;
    }
}
