<?php

namespace App\Http\Requests;

use App\Models\ModelPayload;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyModelPayloadRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('model_payload_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:model_payloads,id',
        ];
    }
}
