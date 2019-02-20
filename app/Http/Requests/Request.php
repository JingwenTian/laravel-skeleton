<?php
/**
 * 入参校验父类.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/12/18 20:42
 */

namespace App\Http\Requests;

use App\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Request.
 *
 * @package App\Http\Requests
 */
class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \App\Exceptions\InvalidArgumentException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $message = array_shift($errors)[0] ?? ''; // 取出第一个错误提示
        throw new InvalidArgumentException($message);
    }
}
