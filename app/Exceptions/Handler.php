<?php

namespace App\Exceptions;

use App\Support\Constant\ELogTopicConst;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     *
     * @throws Exception
     *
     * @return mixed|void
     */
    public function report(Exception $exception)
    {
        //if ($this->shouldReport($exception) && app()->bound('sentry')) {
        //    app('sentry')->captureException($exception);
        //}
        //parent::report($exception);
        if (app()->bound('elog')) {
            app('elog')->notice(
                ELogTopicConst::TOPIC_UNKNOWN, $exception->getMessage(), array_merge($this->context(), ['exception' => $exception])
            );
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        } elseif ($exception instanceof NotFoundHttpException) {
            $exception = new BaseException('not_found_api');
        }
        $response = [
            'code'      => $exception->getCode(),
            'message'   => $exception->getMessage(),
        ];
        if (config('app.debug')) {
            $response['trace'] = $exception->getTraceAsString();
        }

        return response()->json($response);
    }
}
