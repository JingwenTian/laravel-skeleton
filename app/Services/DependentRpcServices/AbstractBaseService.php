<?php
/**
 * Yar Rpc 请求基类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 20:55
 */

namespace App\Services\DependentRpcServices;

use App\Exceptions\NetworkUnavailableException;
use App\Support\Status\Status;
use ELog\Constants;
use Yar_Client;

/**
 * Class AbstractBaseService.
 *
 * @package App\Services\DependentRpcServices
 */
abstract class AbstractBaseService
{
    /**
     * @var Yar_Client
     */
    private $yar;

    /**
     * @var string
     */
    protected $server;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    private $timeout = 10000;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var int
     */
    private $startTime = 0;

    /**
     * @var int
     */
    private $endTime = 0;

    /**
     * @param $method
     * @param array ...$args
     *
     * @return array
     */
    protected function call($method, ...$args): array
    {
        try {
            $this->setError();

            $logs = [
                'method'    => $method,
                'server'    => config($this->server),
                'path'      => $this->path,
                'args'      => $args,
            ];
            app()->elog->info(Constants::TOPIC_RPC, '[RPC]RPC CLIENT发起请求', $logs);

            $uuid = \defined('APP_UUID') ? APP_UUID : '';
            $this->startTime = microtime(true);

            $response = $this->getYar()->entrance($method, $uuid, $args);
            $this->endTime = microtime(true);

            throw_if($response === null, new NetworkUnavailableException('rpc response failed'));
            app()->elog->info(Constants::TOPIC_RPC, '[RPC]RPC CLIENT请求响应成功', ['params' => [
                'method'    => $method,
                'server'    => config($this->server),
                'path'      => $this->path,
            ], 'response' => $response, 'time' => round($this->endTime - $this->startTime, 4)]);
            $code = (int) ($response['code'] ?? 0);
            if ($code === Status::CODE_COMMON_SUCCESS) {
                return (array) ($response['data'] ?? []);
            }
            $this->message = $response['message'] ?? '';
        } catch (\Yar_Server_Exception | \Yar_Client_Exception | \Throwable $exception) {
            $this->message = $exception->getMessage();
            app()->elog->error(Constants::TOPIC_RPC, '[RPC]RPC CLIENT请求响应异常', ['params' => $logs ?? [], 'exception' => $exception]);
        }

        return [];
    }

    /**
     * @param string $message
     */
    public function setError(string $message = null): void
    {
        $this->message = $message ?? '';
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->message);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->message;
    }

    /**
     * @throws NetworkUnavailableException
     *
     * @return Yar_Client
     */
    protected function getYar(): Yar_Client
    {
        if ($this->yar === null) {
            if ($this->server === null || $this->path === null) {
                throw new NetworkUnavailableException('error server_url');
            }
            $this->yar = new Yar_Client(config($this->server).$this->path);
            $this->yar->SetOpt(YAR_OPT_TIMEOUT, $this->timeout);
        }

        return $this->yar;
    }
}
