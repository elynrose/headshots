<?php

namespace App\Http\Requests;

use App\Models\Photo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePhotoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('photo_create');
    }

    public function rules()
    {
        return [
            'photo' => [
                'array',
                'required',
            ],
            'photo.*' => [
                'required',
            ],
            'url' => [
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
