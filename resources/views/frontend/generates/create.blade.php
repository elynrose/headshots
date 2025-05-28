@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center">
        <h3 class="text-2xl font-bold text-gray-900">{{ $modelData->title ?? 'Generate Content' }}</h3>
        <p class="mt-2 text-sm text-gray-500">
            {{ 'Provide the necessary details to proceed.' }}
            @if($fals->model_type === 'prompt')
                Or use a <a href="/generates/create/2" class="text-indigo-600 hover:text-indigo-500">trained model</a>
            @endif
        </p>
    </div>

    <div class="mt-8 max-w-3xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('frontend.generates.store') }}" enctype="multipart/form-data">
                    @csrf

                    @php
                    $formGenerator = new \App\Models\FormGenerator();
                    $html = $formGenerator->generateForm($fals->model_type, $existingImages, $trains);
                    @endphp

                    {!! $html !!}

                    @if($fals->model_type === 'train')
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="train_id">
                                {{ trans('cruds.generate.fields.train') }}
                            </label>
                            <select class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" 
                                    name="train_id" 
                                    id="train_id" 
                                    required>
                                @foreach($trains as $id => $entry)
                                    <option value="{{ $id }}" {{ old('train_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @error('train')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">{{ trans('cruds.generate.fields.train_helper') }}</p>
                        </div>

                        <div>
                            <button type="button" 
                                    class="text-sm text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline"
                                    onclick="document.getElementById('advanced').classList.toggle('hidden')">
                                {{ trans('global.advanced_mode') }}
                            </button>
                        </div>

                        <div id="advanced" class="hidden space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                @foreach(['width' => [512, 576, 640, 704, 768, 832, 896, 960, 1024], 'height' => [512, 576, 640, 704, 768, 832, 896, 960, 1024]] as $field => $options)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2" for="{{ $field }}">
                                            {{ trans("cruds.generate.fields.$field") }}
                                        </label>
                                        <select class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                                name="{{ $field }}" 
                                                id="{{ $field }}">
                                            @foreach($options as $size)
                                                <option value="{{ $size }}">{{ $size }}</option>
                                            @endforeach
                                        </select>
                                        @error($field)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500">{{ trans("cruds.generate.fields.{$field}_helper") }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="inference">
                                    {{ trans('cruds.generate.fields.inference') }}
                                </label>
                                <div class="flex items-center space-x-4">
                                    <input class="w-full" 
                                           type="range" 
                                           name="inference" 
                                           id="inference" 
                                           value="{{ old('inference', '28') }}" 
                                           min="1" 
                                           max="100" 
                                           step="1" 
                                           oninput="this.nextElementSibling.value = this.value">
                                    <output class="text-sm text-gray-700">28</output>
                                </div>
                                @error('inference')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">{{ trans('cruds.generate.fields.inference_helper') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="seed">
                                    {{ trans('cruds.generate.fields.seed') }}
                                </label>
                                <input class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                       type="text" 
                                       name="seed" 
                                       id="seed" 
                                       value="{{ old('seed', '') }}">
                                @error('seed')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">{{ trans('cruds.generate.fields.seed_helper') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-8">      
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

                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-magic mr-2"></i>
                            {{ $fals->title }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
