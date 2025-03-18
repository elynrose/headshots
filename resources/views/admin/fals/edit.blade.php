@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.fal.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.fals.update", [$fal->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="title">{{ trans('cruds.fal.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $fal->title) }}" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.title_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="model_name">{{ trans('cruds.fal.fields.model_name') }}</label>
                <input class="form-control {{ $errors->has('model_name') ? 'is-invalid' : '' }}" type="text" name="model_name" id="model_name" value="{{ old('model_name', $fal->model_name) }}" required>
                @if($errors->has('model_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('model_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.model_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="model_type">{{ trans('cruds.fal.fields.model_type') }}</label>
                <input class="form-control {{ $errors->has('model_type') ? 'is-invalid' : '' }}" type="text" name="model_type" id="model_type" value="{{ old('model_type', $fal->model_type) }}" required>
                @if($errors->has('model_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('model_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.model_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="base_url">{{ trans('cruds.fal.fields.base_url') }}</label>
                <input class="form-control {{ $errors->has('base_url') ? 'is-invalid' : '' }}" type="text" name="base_url" id="base_url" value="{{ old('base_url', $fal->base_url) }}">
                @if($errors->has('base_url'))
                    <div class="invalid-feedback">
                        {{ $errors->first('base_url') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.base_url_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="payload">{{ trans('cruds.fal.fields.payload') }}</label>
                <textarea class="form-control {{ $errors->has('payload') ? 'is-invalid' : '' }}" name="payload" id="payload">{{ old('payload', $fal->payload) }}</textarea>
                @if($errors->has('payload'))
                    <div class="invalid-feedback">
                        {{ $errors->first('payload') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.payload_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="icon">{{ trans('cruds.fal.fields.icon') }}</label>
                <input class="form-control {{ $errors->has('icon') ? 'is-invalid' : '' }}" type="text" name="icon" id="icon" value="{{ old('icon', $fal->icon) }}">
                @if($errors->has('icon'))
                    <div class="invalid-feedback">
                        {{ $errors->first('icon') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.icon_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('enabled') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="enabled" value="0">
                    <input class="form-check-input" type="checkbox" name="enabled" id="enabled" value="1" {{ $fal->enabled || old('enabled', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="enabled">{{ trans('cruds.fal.fields.enabled') }}</label>
                </div>
                @if($errors->has('enabled'))
                    <div class="invalid-feedback">
                        {{ $errors->first('enabled') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.enabled_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.fal.fields.file_type') }}</label>
                <select class="form-control {{ $errors->has('file_type') ? 'is-invalid' : '' }}" name="file_type" id="file_type">
                    <option value disabled {{ old('file_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Fal::FILE_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('file_type', $fal->file_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('file_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('file_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fal.fields.file_type_helper') }}</span>
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