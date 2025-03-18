@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.fal.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.fals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.id') }}
                        </th>
                        <td>
                            {{ $fal->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.title') }}
                        </th>
                        <td>
                            {{ $fal->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.model_name') }}
                        </th>
                        <td>
                            {{ $fal->model_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.model_type') }}
                        </th>
                        <td>
                            {{ $fal->model_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.base_url') }}
                        </th>
                        <td>
                            {{ $fal->base_url }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.payload') }}
                        </th>
                        <td>
                            {{ $fal->payload }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.icon') }}
                        </th>
                        <td>
                            {{ $fal->icon }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.enabled') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $fal->enabled ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.fal.fields.file_type') }}
                        </th>
                        <td>
                            {{ App\Models\Fal::FILE_TYPE_SELECT[$fal->file_type] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.fals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection