@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.edit') }} {{ trans('cruds.train.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.trains.update", [$train->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label for="requestid">{{ trans('cruds.train.fields.requestid') }}</label>
                            <input class="form-control" type="number" name="requestid" id="requestid" value="{{ old('requestid', $train->requestid) }}" step="1">
                            @if($errors->has('requestid'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('requestid') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.requestid_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="title">{{ trans('cruds.train.fields.title') }}</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', $train->title) }}" required>
                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.title_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="status">{{ trans('cruds.train.fields.status') }}</label>
                            <input class="form-control" type="text" name="status" id="status" value="{{ old('status', $train->status) }}">
                            @if($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="diffusers_lora_file">{{ trans('cruds.train.fields.diffusers_lora_file') }}</label>
                            <textarea class="form-control" name="diffusers_lora_file" id="diffusers_lora_file">{{ old('diffusers_lora_file', $train->diffusers_lora_file) }}</textarea>
                            @if($errors->has('diffusers_lora_file'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('diffusers_lora_file') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.diffusers_lora_file_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="config_file">{{ trans('cruds.train.fields.config_file') }}</label>
                            <textarea class="form-control" name="config_file" id="config_file">{{ old('config_file', $train->config_file) }}</textarea>
                            @if($errors->has('config_file'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('config_file') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.config_file_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="file_size">{{ trans('cruds.train.fields.file_size') }}</label>
                            <input class="form-control" type="text" name="file_size" id="file_size" value="{{ old('file_size', $train->file_size) }}">
                            @if($errors->has('file_size'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('file_size') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.file_size_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="error_log">{{ trans('cruds.train.fields.error_log') }}</label>
                            <input class="form-control" type="text" name="error_log" id="error_log" value="{{ old('error_log', $train->error_log) }}">
                            @if($errors->has('error_log'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('error_log') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.error_log_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="user_id">{{ trans('cruds.train.fields.user') }}</label>
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                @foreach($users as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $train->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('user') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.train.fields.user_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection