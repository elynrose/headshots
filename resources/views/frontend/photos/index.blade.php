@extends('layouts.frontend')

@section('content')
<div class="space-y-6" x-data="gallery()">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Your Gallery</h2>
                    <p class="mt-1 text-sm text-gray-500">Browse and manage your generated content.</p>
                </div>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input type="text" 
                               x-model="search" 
                               @input="filterContent()"
                               placeholder="Search content..." 
                               class="form-input pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <select x-model="filter" 
                            @change="filterContent()"
                            class="form-input">
                        <option value="">All Types</option>
                        <option value="image">Images</option>
                        <option value="video">Videos</option>
                        <option value="audio">Audio</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($generates as $generate)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden transform transition-all duration-200 hover:shadow-md"
                 x-show="isVisible('{{ $generate->content_type }}', '{{ strtolower($generate->prompt) }}')">
                <div class="relative">
                    @if($generate->content_type == 'video')
                        <div class="aspect-w-16 aspect-h-9">
                            <video src="{{ $generate->video_url }}" 
                                   class="object-cover"
                                   controls></video>
                        </div>
                    @elseif($generate->content_type == 'audio')
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 flex items-center justify-center">
                            <audio src="{{ $generate->audio_url }}" 
                                   controls
                                   class="w-full"></audio>
                        </div>
                    @else
                        <a href="{{ route('frontend.generates.build', ['model_id' => $generate->fal_model_id, 'generate_id' => $generate->id]) }}" 
                           class="block aspect-w-16 aspect-h-9">
                            <img src="{{ $generate->image_url }}" 
                                 alt="{{ $generate->title }}" 
                                 class="object-cover w-full h-full">
                        </a>
                    @endif

                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $generate->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $generate->status }}
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">{{ $generate->title }}</span>
                        <span class="text-sm text-gray-500">{{ $generate->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $generate->prompt }}</p>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex space-x-2">
                            @if($generate->content_type == 'image')
                                <a href="{{ route('frontend.generates.createWithParent', ['model_id' => 1, 'image_id' => $generate->id]) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                    <i class="fas fa-magic mr-1"></i> Generate
                                </a>
                            @endif
                            <a href="{{ $generate->content_type == 'video' ? $generate->video_url : $generate->image_url }}" 
                               class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200"
                               download>
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>

                        @can('generate_delete')
                            <form action="{{ route('frontend.generates.destroy', $generate->id) }}" 
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

    <!-- Pagination -->
    @if($generates->hasPages())
        <div class="mt-6">
            {{ $generates->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function gallery() {
    return {
        search: '',
        filter: '',
        filterContent() {
            const items = document.querySelectorAll('.grid > div');
            items.forEach(item => {
                const type = item.getAttribute('x-show').match(/'([^']+)'/)[1];
                const prompt = item.getAttribute('x-show').match(/'([^']+)'/)[2].toLowerCase();
                
                const matchesSearch = this.search === '' || prompt.includes(this.search.toLowerCase());
                const matchesFilter = this.filter === '' || type === this.filter;
                
                item.style.display = matchesSearch && matchesFilter ? 'block' : 'none';
            });
        }
    }
}
</script>
@endpush
