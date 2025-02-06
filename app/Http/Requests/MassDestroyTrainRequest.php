<?php

namespace App\Http\Requests;

use App\Models\Train;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTrainRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('train_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:trains,id',
        ];
    }
}
