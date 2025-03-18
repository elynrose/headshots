<?php

namespace App\Http\Requests;

use App\Models\ModelPayload;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateModelPayloadRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('model_payload_edit');
    }

    public function rules()
    {
        return [
            'model_type' => [
                'string',
                'required',
            ],
        ];
    }
}
