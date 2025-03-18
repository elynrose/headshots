<?php

namespace App\Http\Requests;

use App\Models\Fal;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFalRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('fal_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'model_name' => [
                'string',
                'required',
            ],
            'model_type' => [
                'string',
                'required',
            ],
            'base_url' => [
                'string',
                'nullable',
            ],
            'icon' => [
                'string',
                'nullable',
            ],
        ];
    }
}
