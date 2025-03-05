@extends('layouts.frontend')

@section('content')
<div class="container">
    @php
        $titles = [
            'image' => 'Generate an Image',
            'video' => 'Convert Image to Video',
            'audio' => 'Add Lip Sync Audio',
            'upscale' => 'Upscale Video',
        ];
        $descriptions = [
            'image' => 'Describe your image and select a trained model to use',
            'video' => 'Describe the video you want to create',
            'audio' => 'Upload the audio file.',
            'upscale' => 'Upscale the video',
        ];
    @endphp

    <h3 class="text-center">{{ $titles[$fals->model_type] ?? 'Generate Content' }}</h3>
    <p class="text-center mb-5 text-muted">{{ $descriptions[$fals->model_type] ?? 'Provide the necessary details to proceed.' }}</p>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('frontend.generates.store') }}" enctype="multipart/form-data">
                        @csrf

                        @switch($fals->model_type)
                            @case('image')
                            @case('video')
                                <div class="form-group">
                                    <label for="prompt">{{ trans('cruds.generate.fields.prompt') }}</label>
                                    <textarea class="form-control" name="prompt" id="prompt">{{ old('prompt') }}</textarea>
                                    @error('prompt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <span class="help-block small text-muted">{{ trans('cruds.generate.fields.prompt_helper') }}</span>
                                </div>
                                @break

                            @case('audio')
                                <div class="form-group">
                                    <input type="hidden" name="video_url" id="video_url" value="{{ $existingImages->video_url ?? '' }}">
                                    <label for="audio_mp3" class="required">Select Audio File</label>
                                    <input type="file" name="audio_mp3" id="audio_mp3">
                                </div>
                                @break

                                @case('upscale')
                                <div class="form-group">
                                    <input type="hidden" name="video_url" id="video_url" value="{{ $existingImages->video_url ?? '' }}">
                                </div>
                                @break
                        @endswitch

                        @if($fals->model_type === 'image')
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
                        @endif
                        

                        @if(isset($existingImages) && in_array($fals->model_type, ['video', 'audio', 'upscale']))
                            @if(Request::segment(5))
                                <input type="hidden" name="parent" value="{{ Request::segment(5) }}">
                            @endif

                            <div class="form-group">
                                @if($fals->model_type === 'video')
                                    <div id="image-preview" class="mt-3">
                                        <input type="hidden" name="image_url" value="{{ $existingImages->image_url }}">
                                        <img src="{{ $existingImages->image_url }}" alt="Selected Image" class="img-thumbnail w-100">
                                    </div>
                                @elseif($fals->model_type === 'audio' || $fals->model_type === 'upscale')
                                    <div id="video-preview" class="mt-3">
                                        <video width="100%" controls>
                                            <source src="{{ $existingImages->video_url }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($fals->model_type === 'image')
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
                            <input type="hidden" name="credit" value="{{ old('credit', '1') }}">
                            <input type="hidden" name="status" value="NEW">
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            @if(Request::segment(3))
                                <input type="hidden" name="fal_model_id" value="{{ Request::segment(3) }}">
                            @endif

                            <button class="btn btn-default" type="submit">{{ trans('global.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
