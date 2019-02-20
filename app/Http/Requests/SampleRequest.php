<?php

namespace App\Http\Requests;

/**
 * Class SampleRequest.
 *
 * @package App\Http\Requests\External\Roles
 */
class SampleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //'name'                     => 'required|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required'                                   => $this->attributes()['name'].'不能为空',
            'name.max'                                        => $this->attributes()['name'].'长度不能超过100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name'                     => '用户名称',
        ];
    }
}
