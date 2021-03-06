<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleRequest;
use App\Repositories\SampleRepository as Sample;
use App\Support\Constant\ELogTopicConst;
use App\Transformers;
use Illuminate\Http\Request;

/**
 * Class SampleController.
 *
 * @package App\Http\Controllers
 */
class SampleController extends Controller
{
    /**
     * @var Sample
     */
    protected $_repository;

    /**
     * SampleController constructor.
     *
     * @param Sample $sample
     */
    protected function __construct(Sample $sample)
    {
        parent::__construct();
        $this->_repository = $sample;
    }

    /**
     * 示例方法.
     *
     * @param SampleRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sample(SampleRequest $request)
    {
        try {
            $params = $request->all();

            $result = [];
            $result['request_params'] = $params;
            $result['http_sample'] = $this->_repository->httpDependenceSample();

            $result['mysql'] = $this->_repository->mysqlSample();

            $this->_repository->jobsSample();

            //$this->_repository->notificationsSample();

            $this->_repository->eventSample();
        } catch (\Throwable $e) {
            app()->elog->notice(ELogTopicConst::TOPIC_UNKNOWN, '示例请求异常', ['exception' => $e, 'conditions' => $params ?? []]);

            return $this->responseFailure($e->getMessage(), $e->getCode());
        }

        return $this->responseSuccess($result, '查询成功', new Transformers\SampleTransformer());
    }
}
