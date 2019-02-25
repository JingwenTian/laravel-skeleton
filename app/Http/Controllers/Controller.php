<?php

namespace App\Http\Controllers;

use App\Support\Status\Status;
use App\Transformers\TransformerInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var int org_id 主办ID
     */
    protected $clientOrgId = 0;

    /**
     * @var int 子账号 ID
     */
    protected $clientChildId = 0;

    /**
     * @var int user_id 用户ID
     */
    protected $clientUserId = 0;

    /**
     * @var int union_id 平台用户唯一标识
     */
    protected $clientUnionId = '';

    /**
     * @var string session_id
     */
    protected $clientSessionId = '';

    /**
     * @var string 请求uuid
     */
    protected $clientReqId = '';

    /**
     * @var string 客户端ip
     */
    protected $clientIp = '';

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $sysParams = sys_params();
        $this->clientOrgId = $sysParams['client-org-id'] ?? 0;
        $this->clientChildId = $sysParams['client-c-id'] ?? 0;
        $this->clientUserId = $sysParams['client-uid'] ?? 0;
        $this->clientUnionId = $sysParams['client-union-id'] ?? '';
        $this->clientSessionId = $sysParams['client-session-id'] ?? '';
        $this->clientReqId = $sysParams['request-id'] ?? '';
        $this->clientIp = $sysParams['client-ip'] ?? '';

        $this->init();
    }

    /**
     * 初始化.
     */
    protected function init(): void
    {
        \define('APP_UUID', $this->clientReqId);
    }

    /**
     * 成功响应.
     *
     * @param array                     $data
     * @param string                    $message
     * @param TransformerInterface|null $transformer
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function responseSuccess(array $data = [], string $message = '', TransformerInterface $transformer = null)
    {
        if ($transformer !== null) {
            $data = $this->transformData($data, $transformer);
        }
        $result = [
            'message' => $message ?: 'success',
            'code'    => Status::CODE_COMMON_SUCCESS,
            'data'    => $data ?: [],
        ];

        return response($result);
    }

    /**
     * 失败响应.
     *
     * @param string $message
     * @param int    $code
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function responseFailure(string $message, int $code = null)
    {
        $result = [
            'message' => $message ?: 'failed',
            'code'    => $code ?? Status::CODE_COMMON_FAILED,
            'data'    => [],
        ];

        return response($result);
    }

    /**
     * 自定义参数转化器.
     *
     * @param array                $data
     * @param TransformerInterface $transformer
     *
     * @return array
     */
    private function transformData(array $data, TransformerInterface $transformer): array
    {
        $fractal = new Manager();
        $fractal->setSerializer(new ArraySerializer());
        // 关联数组(一维数组)还是索引数组(二维数组), 需要依此返回数据
        // 如果是关联数组则默认为一维数组的转化逻辑
        if (array_keys($data) !== array_keys(array_keys($data))) {
            $resource = new Item($data, $transformer);
            $rootScope = $fractal->createData($resource);

            return $rootScope->toArray();
        }
        // 如果是索引数组则默认为二维数组的转化逻辑
        $resource = new Collection($data, $transformer);
        $rootScope = $fractal->createData($resource);

        return $rootScope->toArray()['data'];
    }
}
