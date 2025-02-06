@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.create') }} {{ trans('cruds.generate.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.generates.store") }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label for="prompt">{{ trans('cruds.generate.fields.prompt') }}</label>
                            <textarea class="form-control" name="prompt" id="prompt">{{ old('prompt') }}</textarea>
                            @if($errors->has('prompt'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('prompt') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.prompt_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="train_id">{{ trans('cruds.generate.fields.train') }}</label>
                            <select class="form-control select2" name="train_id" id="train_id" required>
                                @foreach($trains as $id => $entry)
                                    <option value="{{ $id }}" {{ old('train_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('train'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('train') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.train_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="width">{{ trans('cruds.generate.fields.width') }}</label>
                            <input class="form-control" type="number" name="width" id="width" value="{{ old('width', '') }}" step="1">
                            @if($errors->has('width'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('width') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.width_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="height">{{ trans('cruds.generate.fields.height') }}</label>
                            <input class="form-control" type="number" name="height" id="height" value="{{ old('height', '') }}" step="1">
                            @if($errors->has('height'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('height') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.height_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="status">{{ trans('cruds.generate.fields.status') }}</label>
                            <input class="form-control" type="text" name="status" id="status" value="{{ old('status', '') }}">
                            @if($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="image_url">{{ trans('cruds.generate.fields.image_url') }}</label>
                            <textarea class="form-control" name="image_url" id="image_url">{{ old('image_url') }}</textarea>
                            @if($errors->has('image_url'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('image_url') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.image_url_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="content_type">{{ trans('cruds.generate.fields.content_type') }}</label>
                            <input class="form-control" type="text" name="content_type" id="content_type" value="{{ old('content_type', '') }}">
                            @if($errors->has('content_type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('content_type') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.content_type_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="inference">{{ trans('cruds.generate.fields.inference') }}</label>
                            <input class="form-control" type="text" name="inference" id="inference" value="{{ old('inference', '') }}">
                            @if($errors->has('inference'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('inference') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.inference_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="seed">{{ trans('cruds.generate.fields.seed') }}</label>
                            <input class="form-control" type="text" name="seed" id="seed" value="{{ old('seed', '') }}">
                            @if($errors->has('seed'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('seed') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.seed_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="credit">{{ trans('cruds.generate.fields.credit') }}</label>
                            <input class="form-control" type="number" name="credit" id="credit" value="{{ old('credit', '1') }}" step="1">
                            @if($errors->has('credit'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('credit') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.credit_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="user_id">{{ trans('cruds.generate.fields.user') }}</label>
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                @foreach($users as $id => $entry)
                                    <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('user') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.user_helper') }}</span>
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