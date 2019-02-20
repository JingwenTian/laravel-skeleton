<?php

namespace App\Transformers;

/**
 * Class SampleTransformer 格式化输出示例.
 * @package App\Transformers
 */
class SampleTransformer extends TransformerAbstract
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function transformData(array $data = []): array
    {
        return [
            'request_params' => $data['request_params'] ?? [],
            'http_sample' => [
                'provinces' => collect($data['http_sample']['provinces'] ?? [])->slice(0, 2),
                'country_code' => collect($data['http_sample']['country_code'] ?? [])->slice(0, 1),
            ],
            'mysql' => [
                'pagination' => $data['mysql']['pagination'] ?? [],
                'data' => collect($data['mysql']['data'] ?? [])->slice(0, 2),
            ]
        ];
    }
}
