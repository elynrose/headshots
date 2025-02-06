<?php

namespace App\Http\Requests;

use App\Models\Generate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyGenerateRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('generate_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:generates,id',
        ];
    }
}
