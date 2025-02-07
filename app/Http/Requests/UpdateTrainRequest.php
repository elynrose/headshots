<?php

namespace App\Http\Requests;

use App\Models\Train;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTrainRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('train_edit');
    }

    public function rules()
    {
        return [
            'requestid' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'title' => [
                'string',
                'required',
            ],
            'status' => [
                'string',
                'nullable',
            ],
            'zipped_file_url' => [
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
            'temporary_amz_url' => [
                'string',
                'nullable',
            ],
            'file_size' => [
                'string',
                'nullable',
            ],
            'error_log' => [
                'string',
                'nullable',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
