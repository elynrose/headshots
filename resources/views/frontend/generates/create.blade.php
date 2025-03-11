@extends('layouts.frontend')

@section('content')
<div class="container">

    <h3 class="text-center">{{ $modelData->title ?? 'Generate Content' }}</h3>
    <p class="text-center mb-5 text-muted">{{ 'Provide the necessary details to proceed.' }}</p>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('frontend.generates.store') }}" enctype="multipart/form-data">
                        @csrf

                        @php
                        $formGenerator = new \App\Models\FormGenerator();
                        $html = $formGenerator->generateForm($fals->model_type, $existingImages, $trains);
                        @endphp

                        {!! $html !!}

                        
                        @if($fals->model_type === 'train')
                        <div class="form-group">
                                <label class="required" for="train_id">{{ trans('cruds.generate.fields.train') }}</label>
                                <select class="form-control select" name="train_id" id="train_id" required>
                                    @foreach($trains as $id => $entry)
                                        <option value="{{ $id }}" {{ old('train_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                    @endforeach
                                </select>
                                @error('train')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <span class="help-block small text-muted">{{ trans('cruds.generate.fields.train_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <a href="#advanced" data-toggle="collapse">{{ trans('global.advanced_mode') }}</a>
                            </div>
                            <div id="advanced" class="collapse">
                                <div class="row">
                                    @foreach(['width' => [512, 576, 640, 704, 768, 832, 896, 960, 1024], 'height' => [512, 576, 640, 704, 768, 832, 896, 960, 1024]] as $field => $options)
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="{{ $field }}">{{ trans("cruds.generate.fields.$field") }}</label>
                                                <select class="form-control" name="{{ $field }}" id="{{ $field }}">
                                                    @foreach($options as $size)
                                                        <option value="{{ $size }}">{{ $size }}</option>
                                                    @endforeach
                                                </select>
                                                @error($field)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <span class="help-block small text-muted">{{ trans("cruds.generate.fields.{$field}_helper") }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-group">
                                    <label for="inference">{{ trans('cruds.generate.fields.inference') }}</label>
                                    <input class="form-control" type="range" name="inference" id="inference" value="{{ old('inference', '28') }}" min="1" max="100" step="1" oninput="this.nextElementSibling.value = this.value">
                                    <output>28</output>
                                    @error('inference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <span class="help-block small text-muted">{{ trans('cruds.generate.fields.inference_helper') }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="seed">{{ trans('cruds.generate.fields.seed') }}</label>
                                    <input class="form-control" type="text" name="seed" id="seed" value="{{ old('seed', '') }}">
                                    @error('seed')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <span class="help-block small text-muted">{{ trans('cruds.generate.fields.seed_helper') }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">      
                            @if(Request::segment(5))
                                <input type="hidden" name="parent" value="{{ Request::segment(5) }}">
                            @endif
                            <input type="hidden" name="credit" value="{{ old('credit', '1') }}">
                            <input type="hidden" name="status" value="NEW">
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <input type="hidden" name="content_type" value="{{ $fals->model_type }}">
                            @if(Request::segment(3))
                                <input type="hidden" name="fal_model_id" value="{{ Request::segment(3) }}">
                            @endif

                            <button class="btn btn-default" type="submit">{{ $fals->title }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
