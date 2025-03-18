@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.modelPayload.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.model-payloads.update", [$modelPayload->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="model_type">{{ trans('cruds.modelPayload.fields.model_type') }}</label>
                <input class="form-control {{ $errors->has('model_type') ? 'is-invalid' : '' }}" type="text" name="model_type" id="model_type" value="{{ old('model_type', $modelPayload->model_type) }}" required>
                @if($errors->has('model_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('model_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.modelPayload.fields.model_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="payload_template">{{ trans('cruds.modelPayload.fields.payload_template') }}</label>
                <textarea class="form-control {{ $errors->has('payload_template') ? 'is-invalid' : '' }}" name="payload_template" id="payload_template">{{ old('payload_template', $modelPayload->payload_template) }}</textarea>
                @if($errors->has('payload_template'))
                    <div class="invalid-feedback">
                        {{ $errors->first('payload_template') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.modelPayload.fields.payload_template_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.modelPayload.fields.file_type') }}</label>
                <select class="form-control {{ $errors->has('file_type') ? 'is-invalid' : '' }}" name="file_type" id="file_type">
                    <option value disabled {{ old('file_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\ModelPayload::FILE_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('file_type', $modelPayload->file_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('file_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('file_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.modelPayload.fields.file_type_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection