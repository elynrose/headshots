@extends('layouts.frontend')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Create New Training Model</h2>
        <p class="mt-2 text-sm text-gray-500">
            Select images from your gallery to train your custom AI model.
        </p>
    </div>

    <div class="mt-8 max-w-3xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('frontend.trains.store') }}" x-data="{ selectedImages: [] }">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Model Name
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title', '') }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter a name for your model">
                        @if($errors->has('title'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('title') }}</p>
                        @endif
                    </div>

                    <!-- Image Gallery -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Training Images
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($images as $image)
                                <div class="relative group">
                                    <input type="checkbox" 
                                           name="images[]" 
                                           value="{{ $image->id }}" 
                                           class="absolute top-2 left-2 z-10"
                                           x-model="selectedImages">
                                    <div class="relative aspect-square rounded-lg overflow-hidden cursor-pointer"
                                         :class="{ 'ring-2 ring-indigo-500': selectedImages.includes('{{ $image->id }}') }"
                                         @click="selectedImages.includes('{{ $image->id }}') ? selectedImages = selectedImages.filter(id => id !== '{{ $image->id }}') : selectedImages.push('{{ $image->id }}')">
                                        <img src="{{ $image->url }}" 
                                             alt="{{ $image->name }}" 
                                             class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-opacity"></div>
                                        <div class="absolute top-2 left-2 w-5 h-5 rounded-full bg-white shadow-sm flex items-center justify-center"
                                             :class="{ 'bg-indigo-500': selectedImages.includes('{{ $image->id }}') }">
                                            <svg class="w-4 h-4 text-white" 
                                                 :class="{ 'opacity-100': selectedImages.includes('{{ $image->id }}'), 'opacity-0': !selectedImages.includes('{{ $image->id }}') }"
                                                 fill="none" 
                                                 stroke="currentColor" 
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" 
                                                      stroke-linejoin="round" 
                                                      stroke-width="2" 
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($errors->has('images'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('images') }}</p>
                        @endif
                    </div>

                    <!-- Selected Images Count -->
                    <div class="mb-6" x-show="selectedImages.length > 0">
                        <p class="text-sm text-gray-600">
                            <span x-text="selectedImages.length"></span> images selected
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="selectedImages.length === 0">
                            Start Training
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('training', () => ({
        selectedImages: [],
    }))
})
</script>
@endpush