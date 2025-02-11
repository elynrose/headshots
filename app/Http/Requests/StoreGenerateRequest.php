<?php

namespace App\Http\Requests;

use App\Models\Generate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreGenerateRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('generate_create');
    }

    public function rules()
    {
        return [
            'train_id' => [
                'required',
                'integer',
            ],
            'width' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'requestid' => [
                'string',
                'nullable',
            ],
            'height' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'status' => [
                'string',
                'nullable',
            ],
            'response_url' => [
                'string',
                'nullable',
            ],
            'status_url' => [
                'string',
                'nullable',
            ],
            'cancel_url' => [
                'string',
                'nullable',
            ],
            'queue_position' => [
                'integer',
                'nullable',
            ],
            'content_type' => [
                'string',
                'nullable',
            ],
            'inference' => [
                'string',
                'nullable',
            ],
            'seed' => [
                'string',
                'nullable',
            ],
            'credit' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
