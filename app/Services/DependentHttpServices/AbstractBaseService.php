<?php
/**
 * HTTP请求基类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/20 20:47
 */

namespace App\Services\DependentHttpServices;

use App\Exceptions\NetworkUnavailableException;
use App\Support\Status\Status;
use ELog\Constants;
use GuzzleHttp\Client;
use GuzzleHttp\Exception as Exception;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\RequestOptions;

/**
 * Class AbstractBaseService.
 *
 * @package App\Services\DependentHttpServices
 * @package App\Services\DependentHttpServices
 */
abstract class AbstractBaseService
{
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_POST = 'POST';
    public const HTTP_METHOD_PUT = 'PUT';
    public const HTTP_METHOD_PATCH = 'PATCH';
    public const HTTP_METHOD_DELETE = 'DELETE';

    private const HTTP_REQUEST_KEYS = [
        self::HTTP_METHOD_GET   => 'query',
        self::HTTP_METHOD_POST  => 'json',
    ];

    /**
     * @var array
     */
    private $options = [
        RequestOptions::CONNECT_TIMEOUT => 3, // connect_timeout (seconds)
        RequestOptions::TIMEOUT         => 60, // timeout (seconds)
        RequestOptions::DEBUG           => false, // debug
        RequestOptions::DELAY           => 0, // delay (milliseconds)
        RequestOptions::HTTP_ERRORS     => true, // http_errors
    ];

    /**
     * @var string
     */
    protected $server;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var null
     */
    private $client;

    /**
     * @var null
     */
    private $request;

    /**
     * @var string
     */
    private $errors = '';

    /**
     * @var array
     */
    private $logs = [];

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
     * @param $path
     * @param array ...$args
     *
     * @return array
     */
    protected function call(string $method, string $path, ...$args): array
    {
        try {
            $this->startTime = microtime(true);
            $this->setError();

            $this->logs = [];

            [$body, $queries, $headers] = $args;

            $uuid = \defined('APP_UUID') ? APP_UUID : '';
            $headers = array_merge($headers, ['X-REQUEST-UUID' => $uuid]);

            $this->_createHttpClient($method, $path, $headers);

            $extends = [
                self::HTTP_REQUEST_KEYS[self::HTTP_METHOD_GET] => $queries,
            ];

            if ($method !== self::HTTP_METHOD_GET) {
                $extends[self::HTTP_REQUEST_KEYS[self::HTTP_METHOD_POST]] = $body;
            }
            $extends = array_merge($this->options, $extends);

            // 发起请求日志
            $this->logs += ['method' => $method, 'headers' => $headers, 'params' => $extends];
            app()->elog->info(Constants::TOPIC_SERVER, '[HTTP]HTTP发起请求', $this->logs);

            // 发起请求
            $response = $this->client->send($this->request, $extends);

            $result = $this->_handleResponse($response);

            if ($result['code'] !== Status::CODE_COMMON_SUCCESS) {
                $this->setError($result['message']);
            }

            return $result;
        } catch (Exception\ClientException | Exception\ServerException | Exception\TooManyRedirectsException |
                Exception\RequestException | \Exception $ex) {
            return $this->_handleException($ex);
        }
    }

    /**
     * @param string $message
     */
    protected function setError(string $message = null): void
    {
        $this->errors = $message ?? '';
    }

    /**
     * @return bool
     */
    protected function hasError(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return string
     */
    protected function getError(): string
    {
        return $this->errors;
    }

    /**
     * @return float
     */
    protected function getTime(): float
    {
        return round($this->endTime - $this->startTime, 4);
    }

    /**
     * @param $method
     * @param $path
     * @param array $headers
     *
     * @throws NetworkUnavailableException
     */
    private function _createHttpClient(string $method, string $path, array $headers = null): void
    {
        if ($this->server === null || $this->path === null) {
            throw new NetworkUnavailableException('backend service required');
        }

        $baseUri = config($this->server);
        $requestPath = sprintf('%s/%s', $this->path, $path);

        $this->logs['base_uri'] = $baseUri;
        $this->logs['path'] = $requestPath;

        $this->client = new Client(['base_uri' => $baseUri]);
        $this->request = new Psr7\Request($method, $requestPath, $headers ?? []);
    }

    /**
     * 异常结果处理.
     *
     * @param $exception
     *
     * @return array
     */
    private function _handleException(\Throwable $exception): array
    {
        $this->endTime = microtime(true);
        $this->logs += [
            'time'      => $this->getTime(),
            'code'      => $exception->getCode(),
            'message'   => $exception->getMessage(),
        ];

        $this->setError($exception->getMessage());

        if ($exception instanceof Exception\ClientException) {
            $this->logs['level'] = 'ClientException';
            $this->logs['description'] = '40x level errors';
        } elseif ($exception instanceof Exception\ServerException) {
            $this->logs['level'] = 'ServerException';
            $this->logs['description'] = '50x level erros';
        } elseif ($exception instanceof Exception\TooManyRedirectsException) {
            $this->logs['level'] = 'TooManyRedirectsException';
            $this->logs['description'] = 'too many redirects are followed';
        } elseif ($exception instanceof Exception\RequestException) {
            $this->logs['level'] = 'RequestException';
            $this->logs['description'] = 'networking error (connection timeout, DNS errors, etc.)';
            $this->logs['method'] = $exception->getRequest()->getMethod();
            $this->logs['request'] = Psr7\str($exception->getRequest());
            $this->logs['response'] = $exception->hasResponse() ? Psr7\str($exception->getResponse()) : '';
        } else {
            $this->logs['level'] = 'Exception';
            $this->logs['description'] = $exception->getMessage() ?: 'unknown error';
        }

        // 请求网络异常日志
        app()->elog->error(Constants::TOPIC_SERVER, 'http response exception', $this->logs);

        return  [
            'code'      => Status::CODE_COMMON_RPC_ERROR,
            'message'   => '请求异常',
        ];
    }

    /**
     * 响应结果处理.
     *
     * @param $response
     *
     * @throws NetworkUnavailableException
     *
     * @return array
     */
    private function _handleResponse($response): array
    {
        $this->endTime = microtime(true);
        $content = $response->getBody()->getContents();

        // 接收响应的元数据日志
        $this->logs += ['time' => $this->getTime(), 'content' => $content];
        app()->elog->debug(Constants::TOPIC_SERVER, '[HTTP]HTTP请求响应元数据', $this->logs);

        // 解析响应数据
        if (\json_decode($content) === null) {
            // 数据解析错误日志
            $this->logs += ['message' => '返回值格式不正确'];
            app()->elog->error(Constants::TOPIC_SERVER, '[HTTP]HTTP响应数据解析错误', $this->logs);

            throw new NetworkUnavailableException('json parse failed');
        }

        $content = \json_decode($content, true);

        // 解析成功的日志
        $this->logs = [
            'time'      => $this->getTime(),
            'code'      => $response->getStatusCode(),
            'message'   => $response->getReasonPhrase(),
            'data'      => $content,
        ];

        app()->elog->info(Constants::TOPIC_SERVER, '[HTTP]HTTP请求响应成功', $this->logs);

        $code = (int) array_get($content, 'code', 0);

        return [
            'code'      => $code === Status::CODE_COMMON_SUCCESS ? Status::CODE_COMMON_SUCCESS : Status::CODE_COMMON_RESOURCE_NOT_EXIST,
            'message'   => $content['message'] ?? '',
            'data'      => $content['data'] ?? [],
        ];
    }
}
