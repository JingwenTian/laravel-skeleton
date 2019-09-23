<?php
/**
 * HTTP并行请求基类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/9/26 18:28
 */

namespace App\Services\DependentHttpServices;

use App\Exceptions\NetworkUnavailableException;
use App\Support\Status\Status;
use ELog\Constants;
use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\RequestOptions;

/**
 * Class AbstractConcurrentBaseService.
 *
 * @package App\Services\DependentHttpServices
 */
abstract class AbstractConcurrentBaseService
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
     * @var null
     */
    private $client;
    /**
     * @var null
     */
    private $request;
    /**
     * @var null
     */
    private $response;
    /**
     * @var null
     */
    private $content;
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
     * 并行请求.
     *
     * @param $method
     * @param $path
     * @param array ...$args
     * @param array $requests
     *
     * @return self
     */
    protected function call(array $requests): self
    {
        try {
            $this->startTime = microtime(true);
            $this->setError();
            $this->logs = [];

            $this->_createHttpClient();

            $promises = [];
            foreach ($requests as $request) {
                $method = $request['method'] ?? self::HTTP_METHOD_GET;
                $requestPath = sprintf('%s/%s', $this->path, $request['path']);

                $headers = array_merge($request['headers'] ?? [], ['X-REQUEST-UUID' => \defined('APP_UUID') ? APP_UUID : '', 'Accept-Language' => app()->getLocale()]);
                $extends = [
                    self::HTTP_REQUEST_KEYS[self::HTTP_METHOD_GET] => $request['queries'] ?? [],
                    'headers'                                      => $headers,
                ];
                if ($method !== self::HTTP_METHOD_GET) {
                    $extends[self::HTTP_REQUEST_KEYS[self::HTTP_METHOD_POST]] = $request['body'] ?? [];
                }
                $extends = array_merge($this->options, $request['options'] ?? [], $extends);

                $this->logs['promises'][] = ['method' => $method, 'path' => $requestPath, 'params' => $extends];
                $promises[] = $this->client->requestAsync($method, $requestPath, $extends);
            }

            // 发起请求日志
            app()->elog->info(Constants::TOPIC_SERVER, '[HTTP]HTTP发起请求', $this->logs);

            // Wait on all of the requests to complete. Throws a ConnectException
            // if any of the requests fail
            $this->response = Promise\unwrap($promises);

            $this->content = $this->_handleResponse();
        } catch (Exception\ClientException | Exception\ServerException | Exception\TooManyRedirectsException | Exception\RequestException | \Exception $ex) {
            $this->_handleException($ex);
        }

        return $this;
    }

    /**
     * 响应数组.
     *
     * @return array
     */
    protected function toArray(): array
    {
        $content = collect($this->content)->map(function ($item) {
            $item = \json_decode($item, true);

            return \json_last_error() === JSON_ERROR_NONE ? $item : [];
        })->toArray();

        return [
            'code'      => Status::CODE_COMMON_SUCCESS,
            'message'   => $this->errors ?: 'success',
            'data'      => $content,
        ];
    }

    /**
     * 响应 Json.
     *
     * @return string
     */
    protected function toJson(): string
    {
        $content = collect($this->content)->map(function ($item) {
            $item = \json_decode($item, true);

            return \json_last_error() === JSON_ERROR_NONE ? $item : [];
        })->toArray();

        return \json_encode($content);
    }

    /**
     * 响应元数据.
     *
     * @return mixed
     */
    protected function toBase()
    {
        return $this->content;
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
     * @throws NetworkUnavailableException
     */
    private function _createHttpClient(): void
    {
        if ($this->server === null || $this->path === null) {
            throw new NetworkUnavailableException('backend service required');
        }

        $baseUri = config($this->server);

        $this->logs['base_uri'] = $baseUri;

        $this->client = new Client(['base_uri' => $baseUri]);
    }

    /**
     * 异常结果处理.
     *
     * @param $exception
     *
     * @return string
     */
    private function _handleException($exception): string
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

        $this->content = \json_encode([
            'code'      => Status::CODE_COMMON_RPC_ERROR,
            'message'   => '请求异常',
        ]);

        return $this->content;
    }

    /**
     * 响应结果处理.
     *
     * @throws NetworkUnavailableException
     *
     * @return array
     */
    private function _handleResponse(): array
    {
        $this->endTime = microtime(true);

        array_map(function ($response) {
            $this->logs['response'][] = [
                'code'      => $response->getStatusCode(),
                'message'   => $response->getReasonPhrase(),
            ];
            $this->content[] = $response->getBody()->getContents();
        }, (array) $this->response);

        // 接收响应的元数据日志
        $this->logs += [
            'time'      => $this->getTime(),
            'content'   => $this->content,
        ];
        app()->elog->info(Constants::TOPIC_SERVER, '[HTTP]HTTP请求响应成功', $this->logs);

        return (array) $this->content;
    }
}
