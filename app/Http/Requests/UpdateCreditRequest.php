<?php

namespace App\Http\Requests;

use App\Models\Credit;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCreditRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('credit_edit');
    }

    public function rules()
    {
        return [
            'points' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'email' => [
                'required',
                'unique:credits,email,' . request()->route('credit')->id,
            ],
        ];
    }
}
