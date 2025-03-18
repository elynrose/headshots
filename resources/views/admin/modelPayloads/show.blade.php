@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.modelPayload.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.model-payloads.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.modelPayload.fields.model_type') }}
                        </th>
                        <td>
                            {{ $modelPayload->model_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.modelPayload.fields.payload_template') }}
                        </th>
                        <td>
                            {{ $modelPayload->payload_template }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.modelPayload.fields.file_type') }}
                        </th>
                        <td>
                            {{ App\Models\ModelPayload::FILE_TYPE_SELECT[$modelPayload->file_type] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.model-payloads.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection