@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

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
                            <span class="help-block small text-muted">{{ trans('cruds.generate.fields.prompt_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="train_id">{{ trans('cruds.generate.fields.train') }}</label>
                            <select class="form-control select" name="train_id" id="train_id" required>
                                @foreach($trains as $id => $entry)
                                    <option value="{{ $id }}" {{ old('train_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('train'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('train') }}
                                </div>
                            @endif
                            <span class="help-block small text-muted">{{ trans('cruds.generate.fields.train_helper') }}</span>
                        </div>
                  
                        <div class="form-group">
                            <a href="#advanced" class="btn btn-link" data-toggle="collapse">{{ trans('global.advanced_mode') }}</a>
                        </div>
                        <div id="advanced" class="collapse">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="width">{{ trans('cruds.generate.fields.width') }}</label>
                                        <select class="form-control" name="width" id="width">
                                            <option value="512">512</option>
                                            <option value="576">576</option>
                                            <option value="640">640</option>
                                            <option value="704">704</option>
                                            <option value="768">768</option>
                                            <option value="832">832</option>
                                            <option value="896">896</option>
                                            <option value="960">960</option>
                                            <option value="1024">1024</option>
                                        </select>
                                        @if($errors->has('width'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('width') }}
                                            </div>
                                        @endif
                                        <span class="help-block small text-muted">{{ trans('cruds.generate.fields.width_helper') }}</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="height">{{ trans('cruds.generate.fields.height') }}</label>
                                        <select class="form-control" name="height" id="height">
                                            <option value="512">512</option>
                                            <option value="576">576</option>
                                            <option value="640">640</option>
                                            <option value="704">704</option>
                                            <option value="768">768</option>
                                            <option value="832">832</option>
                                            <option value="896">896</option>
                                            <option value="960">960</option>
                                            <option value="1024">1024</option>
                                        </select>
                                        @if($errors->has('height'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('height') }}
                                            </div>
                                        @endif
                                        <span class="help-block small text-muted">{{ trans('cruds.generate.fields.height_helper') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inference">{{ trans('cruds.generate.fields.inference') }}</label>
                                <input class="form-control" type="range" name="inference" id="inference" value="{{ old('inference', '28') }}" min="1" max="100" step="1" oninput="this.nextElementSibling.value = this.value">
                                <output>28</output>
                                @if($errors->has('inference'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('inference') }}
                                    </div>
                                @endif
                                <span class="help-block small text-muted">{{ trans('cruds.generate.fields.inference_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="seed">{{ trans('cruds.generate.fields.seed') }}</label>
                                <input class="form-control" type="text" name="seed" id="seed" value="{{ old('seed', '') }}">
                                @if($errors->has('seed'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('seed') }}
                                    </div>
                                @endif
                                <span class="help-block small text-muted">{{ trans('cruds.generate.fields.seed_helper') }}</span>
                            </div>
                        </div>
                     
                        <div class="form-group">
                        <input class="form-control" type="hidden" name="credit" id="credit" value="{{ old('credit', '1') }}" step="1">
                        <input type="hidden" name="status" id="status" value="NEW">
                        <input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() }}">    
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