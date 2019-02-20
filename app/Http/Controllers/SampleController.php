<?php
namespace App\Http\Controllers;

use App\Http\Requests\SampleRequest;
use App\Support\Constant\ELogTopicConst;
use App\Transformers;
use App\Repositories\SampleRepository;
use Illuminate\Http\Request;

/**
 * Class SampleController.
 *
 * @package App\Http\Controllers
 */
class SampleController extends Controller
{
    /**
     * @var \App\Repositories\SampleRepository
     */
    protected $_repository;

    /**
     * SampleController constructor.
     */
    protected function init(): void
    {
        parent::init();
        $this->_repository = app(SampleRepository::class);
    }

    /**
     * 获取菜单.
     *
     * @api {get} /external/menus [菜单]获取主办导航菜单.
     * @apiVersion 1.0.0
     * @apiName editionServiceItem
     * @apiGroup External-Menu
     * @apiHeader {String} Authorization Users Auth Token.
     *
     * @apiDescription 用于主办后台左导航菜单, 包含当前主办信息、版本、菜单等
     *
     * @apiParam {String} topic 终端类型(web PC控制台;app 活动易APP; weapp活动易小程序; pdd 票大大)
     * @apiParam {String} version 控制台版本号(以实际为准,不传则采用默认)
     *
     * @apiSuccess {Object} org 主办基础信息
     * @apiSuccess {String} org.account_type 账号类型(org 主账号 staff 子账号)
     * @apiSuccess {String} org.org_id 主办ID
     * @apiSuccess {String} org.staff_id 子账号ID
     * @apiSuccess {String} org.org_logo 主办头像
     * @apiSuccess {String} org.org_name 主办名称
     * @apiSuccess {Object} version 当前控制台版本号
     * @apiSuccess {Array} editions 当前主办开通的版本和功能
     * @apiSuccess {Array} menus 当前菜单
     * @apiSuccess {String} menus.parent_code 父集权限别名
     * @apiSuccess {String} menus.module_code 模块别名
     * @apiSuccess {String} menus.permission_code 权限别名(唯一标识)
     * @apiSuccess {String} menus.name 权限名称
     * @apiSuccess {String} menus.type 权限类型(1菜单2页面3版块4功能5按钮)
     * @apiSuccess {String} menus.path 权限跳转路径
     * @apiSuccess {String} menus.icon 权限图标(暂无)
     * @apiSuccess {Array} menus.tags 权限标签['new']
     * @apiSuccess {Object} menus.options 扩展属性
     * @apiSuccess {String} menus.options.version 对应的系统(2.0或3.0)
     * @apiSuccess {String} menus.options.target 地址打开方式(self 当前页打开, blank 新页打开 none无打开行为)
     * @apiSuccess {String} menus.options.domain 服务主域标识
     * @apiSuccess {String} menus.options.denied_type 无权限提示类型(error_page无权限页、popup弹窗提示、buy_editions引导升级版本、buy_functions引导订购功能)
     * @apiSuccess {String} menus.options.denied_tips 无权限提示语
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

        } catch (\Throwable $e) {
            app()->elog->notice(ELogTopicConst::TOPIC_UNKNOWN, '示例请求异常', ['exception' => $e, 'conditions' => $params ?? []]);

            return $this->responseFailure($e->getMessage(), $e->getCode());
        }

        return $this->responseSuccess($result, '查询成功', new Transformers\SampleTransformer());
    }
}
