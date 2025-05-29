@extends('layouts.frontend')

@section('content')
<div class="space-y-6" x-data="gallery()">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Your Photos</h2>
                    <p class="mt-1 text-sm text-gray-500">Browse and manage your uploaded photos.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" 
                               x-model="search" 
                               @input="filterContent()"
                               placeholder="Search photos..." 
                               class="form-input pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <a href="{{ route('frontend.photos.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Photo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Selection Info -->
    <div class="bg-white shadow-sm rounded-lg p-4" x-show="selectedPhotos.length > 0">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-900">
                    <span x-text="selectedPhotos.length"></span> photos selected
                </span>
                <button @click="clearSelection()" 
                        class="text-sm text-gray-500 hover:text-gray-700">
                    Clear selection
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="selectAll()" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                    Select All
                </button>
                <button @click="deselectAll()" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
                    Deselect All
                </button>
            </div>
        </div>
    </div>

    <!-- Required Field Message -->
    <div class="bg-red-50 border-l-4 border-red-400 p-4" x-show="showRequiredMessage">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    Please select at least one photo to proceed.
                </p>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($photos as $photo)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden transform transition-all duration-200 hover:shadow-md"
                 x-show="isVisible('{{ strtolower($photo->name ?? '') }}')"
                 :class="{ 'ring-2 ring-indigo-500': isSelected({{ $photo->id }}) }">
                <div class="relative">
                    <!-- Selection Checkbox -->
                    <div class="absolute top-2 left-2 z-10">
                        <input type="checkbox" 
                               :checked="isSelected({{ $photo->id }})"
                               @change="toggleSelection({{ $photo->id }})"
                               class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </div>
                    
                    <a href="{{ route('frontend.photos.show', $photo->id) }}" 
                       class="block aspect-w-16 aspect-h-9">
                        @foreach($photo->photo as $media)
                            <img src="{{ $media->getUrl() }}" 
                                 alt="{{ $photo->name ?? 'Photo ' . $photo->id }}" 
                                 class="object-cover w-full h-full" style="max-height: 200px;">
                        @endforeach
                    </a>
                </div>

                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">{{ $photo->name ?? 'Photo ' . $photo->id }}</span>
                        <span class="text-sm text-gray-500">{{ $photo->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex space-x-2">
                            <a href="{{ route('frontend.photos.edit', $photo->id) }}"
                               class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <a href="{{ $photo->photo->first()->getUrl() }}" 
                               class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200"
                               download>
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>

                        @can('photo_delete')
                            <form action="{{ route('frontend.photos.destroy', $photo->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
function gallery() {
    return {
        search: '',
        selectedPhotos: [],
        showRequiredMessage: false,
        
        isVisible(prompt) {
            return this.search === '' || prompt.includes(this.search.toLowerCase());
        },
        
        filterContent() {
            const items = document.querySelectorAll('.grid > div');
            items.forEach(item => {
                const prompt = item.getAttribute('x-show').match(/'([^']+)'/)[1].toLowerCase();
                item.style.display = this.isVisible(prompt) ? 'block' : 'none';
            });
        },
        
        isSelected(photoId) {
            return this.selectedPhotos.includes(photoId);
        },
        
        toggleSelection(photoId) {
            const index = this.selectedPhotos.indexOf(photoId);
            if (index === -1) {
                this.selectedPhotos.push(photoId);
            } else {
                this.selectedPhotos.splice(index, 1);
            }
            this.showRequiredMessage = false;
        },
        
        clearSelection() {
            this.selectedPhotos = [];
            this.showRequiredMessage = true;
        },
        
        selectAll() {
            const visiblePhotos = Array.from(document.querySelectorAll('.grid > div'))
                .filter(div => div.style.display !== 'none')
                .map(div => {
                    const checkbox = div.querySelector('input[type="checkbox"]');
                    return parseInt(checkbox.getAttribute('@change').match(/\d+/)[0]);
                });
            this.selectedPhotos = [...new Set([...this.selectedPhotos, ...visiblePhotos])];
            this.showRequiredMessage = false;
        },
        
        deselectAll() {
            this.selectedPhotos = [];
            this.showRequiredMessage = true;
        },

        validateSelection() {
            if (this.selectedPhotos.length === 0) {
                this.showRequiredMessage = true;
                return false;
            }
            return true;
        }
    }
}
</script>
@endsection
