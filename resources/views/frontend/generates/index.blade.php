@extends('layouts.frontend')
@section('content')
<div class="container-fluid px-4 py-5">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                <h1 class="text-2xl font-bold text-gray-900">My Generations</h1>
                <p class="mt-1 text-sm text-gray-500">View and manage your generated content</p>
                                            </div>
            <div class="flex items-center gap-4">
                <!-- Stats -->
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <span class="block text-sm font-medium text-gray-500">Total</span>
                        <span class="block text-xl font-semibold text-gray-900">{{ $generates->total() }}</span>
                                            </div>
                    <div class="text-center">
                        <span class="block text-sm font-medium text-gray-500">In Progress</span>
                        <span class="block text-xl font-semibold text-indigo-600">{{ $generates->whereIn('status', ['NEW', 'IN_QUEUE', 'IN_PROGRESS'])->count() }}</span>
                                         </div>
                                        </div>
                <!-- New Generation Button -->
                @can('generate_create')
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> New Generation
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            @foreach($fals as $model)
                            <a href="{{ route('frontend.generates.create', ['model_id' => $model->id]) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas {{ $model->icon ?? 'fa-cog' }} mr-2"></i> {{ $model->title }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       placeholder="Search generations..." 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <!-- Filters -->
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-sort mr-2"></i> Sort
                </button>
            </div>
        </div>
    </div>

    <!-- Grid Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($generates as $generate)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') waiting animate-pulse @endif generate_{{$generate->id}}" 
             data-id="{{ $generate->id }}"
             data-type="{{ $generate->content_type }}">
            <!-- Media Preview -->
            <div class="relative aspect-w-16 aspect-h-9">
                @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS')
                <div class="loading-gif loader_{{$generate->id}} absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 z-10">
                    <div class="text-center">
                        <img width="50" src="{{asset('images/loader.gif')}}" alt="Loading...">
                        <div class="mt-2 text-sm text-gray-600">
                            <span id="queue_{{$generate->id}}">Queue position: {{ $generate->queue_position ?? 'Waiting...' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($generate->content_type == 'video' || $generate->content_type == 'audio')
                    <span class="absolute top-2 left-2 px-2 py-1 text-xs font-medium text-white bg-green-600 rounded-full z-10">
                        <i class="fas fa-video"></i>
                    </span>
                    <video src="{{$generate->video_url ?? ''}}" 
                           controls 
                           loop 
                           class="w-full h-full object-cover video_{{$generate->id}}"
                           @if($generate->status!=='COMPLETED') style="display:none;" @endif>
                    </video>
                @elseif($generate->content_type == 'image' || $generate->content_type == 'prompt' || $generate->content_type == 'train')
                    <span class="absolute top-2 left-2 px-2 py-1 text-xs font-medium text-white bg-yellow-600 rounded-full z-10">
                        <i class="fas fa-image"></i> {{ $generate->fal->model_name}}
                    </span>
                    <a href="{{route('frontend.generates.build', ['model_id'=>$generate->fal_model_id, 'generate_id'=>$generate->id])}}" 
                       class="block w-full h-full">
                        <img src="{{ $generate->image_url ?? asset('images/loading.gif') }}" 
                             class="w-full h-full object-cover image_{{$generate->id}}" 
                             alt="{{$generate->title}}" 
                             loading="lazy">
                    </a>
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                        <span class="text-gray-500">Model Undefined</span>
                    </div>
                @endif
            </div>
         
            <!-- Card Body -->
            <div class="p-4">
                <!-- Status and Actions -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($generate->status == 'COMPLETED') bg-green-100 text-green-800
                            @elseif($generate->status == 'ERROR') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            <span id="status_{{$generate->id}}">{{ $generate->status ?? '' }}</span>
                        </span>
                        <button type="button" 
                                class="refresh-status inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                data-id="{{$generate->id}}">
                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($generate->content_type == 'image' || $generate->content_type == 'prompt' || $generate->content_type == 'train')
                            <a href="{{ $generate->image_url ?? '' }}" 
                               class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full"
                               download>
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{route('frontend.generates.build', ['model_id'=>$generate->fal_model_id, 'generate_id'=>$generate->id])}}" 
                               class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                                <i class="fas fa-random"></i>
                            </a>
                        @elseif($generate->content_type == 'video' || $generate->content_type == 'audio')
                            <a href="{{ $generate->video_url ?? '' }}" 
                               class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full"
                               download>
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @can('generate_delete')
                            <form action="{{ route('frontend.generates.destroy', $generate->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:text-red-900 hover:bg-red-100 rounded-full">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <!-- Metadata -->
                <div class="space-y-1 text-sm text-gray-500">
                    <div class="flex items-center">
                        <i class="fas fa-clock w-4"></i>
                        <span class="ml-2">{{ $generate->created_at->diffForHumans() ?? '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-coins w-4"></i>
                        <span class="ml-2">{{ $generate->credit ?? '0' }} Credits</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        </div>
     
    <!-- Pagination -->
    <div class="mt-8">
        <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
            <div class="-mt-px flex w-0 flex-1">
                @if($generates->onFirstPage())
                    <span class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500">
                        <i class="fas fa-chevron-left mr-3"></i>
                        Previous
                    </span>
                @else
                    <a href="{{ $generates->previousPageUrl() }}" class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        <i class="fas fa-chevron-left mr-3"></i>
                        Previous
                    </a>
                @endif
            </div>
            <div class="hidden md:-mt-px md:flex">
                @foreach($generates->getUrlRange(1, $generates->lastPage()) as $page => $url)
                    @if($page == $generates->currentPage())
                        <span class="inline-flex items-center border-t-2 border-indigo-500 px-4 pt-4 text-sm font-medium text-indigo-600">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            </div>
            <div class="-mt-px flex w-0 flex-1 justify-end">
                @if($generates->hasMorePages())
                    <a href="{{ $generates->nextPageUrl() }}" class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        Next
                        <i class="fas fa-chevron-right ml-3"></i>
                    </a>
                @else
                    <span class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500">
                        Next
                        <i class="fas fa-chevron-right ml-3"></i>
                    </span>
                @endif
            </div>
        </nav>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track active polling intervals
    const activePolling = new Map();
    const POLLING_INTERVAL = 5000; // 5 seconds
    const MAX_POLLING_TIME = 5 * 60 * 1000; // 5 minutes
    const RATE_LIMIT_DELAY = 1000; // 1 second between requests
    let lastRequestTime = 0;

    // Initial status check for all items
    document.querySelectorAll('[data-id]').forEach(element => {
        const generateId = element.dataset.id;
        const statusElement = document.getElementById('status_' + generateId);
        const currentStatus = statusElement?.textContent;
        const hasImage = hasValidImage(element);
        
        // Start polling if item is in progress or completed but image not loaded
        if (statusElement && (
            ['NEW', 'IN_QUEUE', 'IN_PROGRESS'].includes(currentStatus) ||
            (currentStatus === 'COMPLETED' && !hasImage)
        )) {
            startPolling(element, generateId);
        }
    });

    // Add click handlers for refresh buttons
    document.querySelectorAll('.refresh-status').forEach(button => {
        button.addEventListener('click', function() {
            const generateId = this.dataset.id;
            const element = document.querySelector(`[data-id="${generateId}"]`);
            if (element) {
                startPolling(element, generateId);
            }
        });
    });

    function hasValidImage(element) {
        const generateId = element.dataset.id;
        const imageElement = document.querySelector('.image_' + generateId);
        return imageElement && imageElement.src && 
               imageElement.src !== window.location.href && 
               !imageElement.src.includes('loading.gif');
    }

    function startPolling(element, generateId) {
        if (activePolling.has(generateId)) {
            clearInterval(activePolling.get(generateId));
        }

        const startTime = Date.now();
        const intervalId = setInterval(async () => {
            if (Date.now() - startTime > MAX_POLLING_TIME) {
                stopPolling(generateId);
                return;
            }

            try {
                await checkStatus(element, generateId);
                
                const statusElement = document.getElementById('status_' + generateId);
                const currentStatus = statusElement?.textContent;
                const hasImage = hasValidImage(element);
                
                if (currentStatus === 'COMPLETED' && hasImage) {
                    stopPolling(generateId);
                } else if (currentStatus === 'ERROR') {
                    stopPolling(generateId);
                }
            } catch (error) {
                stopPolling(generateId);
            }
        }, POLLING_INTERVAL);

        activePolling.set(generateId, intervalId);
    }

    function stopPolling(generateId) {
        if (activePolling.has(generateId)) {
            clearInterval(activePolling.get(generateId));
            activePolling.delete(generateId);
        }
    }

    async function waitForRateLimit() {
        const now = Date.now();
        const timeSinceLastRequest = now - lastRequestTime;
        if (timeSinceLastRequest < RATE_LIMIT_DELAY) {
            await new Promise(resolve => setTimeout(resolve, RATE_LIMIT_DELAY - timeSinceLastRequest));
        }
        lastRequestTime = Date.now();
}

    function showLoading(element) {
        const generateId = element.dataset.id;
        const loader = element.querySelector('.loader_' + generateId);
        if (loader) {
            loader.style.display = 'flex';
        }
        element.classList.add('animate-pulse');
    }

    function hideLoading(element) {
        const generateId = element.dataset.id;
        const loader = element.querySelector('.loader_' + generateId);
        if (loader) {
            loader.style.display = 'none';
        }
        element.classList.remove('animate-pulse');
    }

    async function checkStatus(element, generateId) {
        try {
            showLoading(element);
            await waitForRateLimit();
            
            const response = await fetch('{{ route('frontend.generates.status') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id: generateId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
}

            const data = await response.json();

            if (!data) {
                throw new Error('No response data received');
            }

            if (data.error) {
                throw new Error(data.error);
            }

            // Update queue position if available
            if (data.queue_position !== undefined) {
                const queueElement = document.getElementById('queue_' + generateId);
                if (queueElement) {
                    queueElement.textContent = 'Queue position: ' + (data.queue_position || 'Waiting...');
                }
            }

            // Update status display
            const statusElement = document.getElementById('status_' + generateId);
            if (statusElement) {
                statusElement.textContent = data.status;
            }

            // Handle different status states
            if (data.status === 'COMPLETED') {
                element.classList.remove('waiting');
                
                if (data.image_url) {
                    handleImageContent(element, generateId, data);
                } else {
                    hideLoading(element);
                }
                
                if (!hasValidImage(element)) {
                    return;
                }
            } else if (data.status === 'ERROR') {
                element.classList.remove('waiting');
                element.classList.add('error');
                hideLoading(element);
            } else if (['NEW', 'IN_QUEUE', 'IN_PROGRESS'].includes(data.status)) {
                element.classList.add('waiting');
            }
        } catch (error) {
            const statusElement = document.getElementById('status_' + generateId);
            if (statusElement) {
                statusElement.textContent = 'ERROR: ' + error.message;
            }
            element.classList.add('error');
            throw error;
        } finally {
            hideLoading(element);
        }
    }

    function handleImageContent(element, generateId, data) {
        const imageElement = document.querySelector('.image_' + generateId);
        
        if (!imageElement) {
            return;
      }

        if (!data.image_url) {
            return;
        }

        const timestamp = new Date().getTime();
        const imageUrl = data.image_url + "?t=" + timestamp;
        
        // Show and load image
        imageElement.style.display = 'block';
        imageElement.src = imageUrl;
        
        // Hide loading indicators
        document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'none');

        // Add event listeners for image loading
        imageElement.onload = () => {
            imageElement.style.display = 'block';
            document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'none');
            stopPolling(generateId);
        };

        imageElement.onerror = () => {
            element.classList.add('error');
            document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'none');
            stopPolling(generateId);
        };
    }
});
</script>
@endsection