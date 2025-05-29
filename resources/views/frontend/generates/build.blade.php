@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Generated Content</h3>
                <p class="mt-1 text-sm text-gray-500">View and manage your generated content.</p>
            </div>
            <!-- Debug Info -->
            <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">
                <p>Generate ID: {{ $generate->id ?? 'Not set' }}</p>
                <p>Status: {{ $generate->status ?? 'Not set' }}</p>
                <p>Content Type: {{ $generate->content_type ?? 'Not set' }}</p>
            </div>
        </div>
    </div>

    <!-- Parent Content Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
        <div class="p-6 @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') animate-pulse @endif" 
             data-id="{{ $generate->id }}"
             data-type="{{ $generate->content_type }}">
            <!-- Content Type Badge -->
            @if(in_array($generate->content_type, $gen->videoTypes()))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mb-4">
                    <i class="fas fa-video mr-2"></i> {{ $generate->fal->model_name}}
                </span>
            @elseif(in_array($generate->content_type, $gen->imageTypes()))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mb-4">
                    <i class="fas fa-photo mr-2"></i> {{ $generate->fal->model_name}}
                </span>
            @endif

            <!-- Media Container -->
            <div class="relative rounded-lg overflow-hidden bg-gray-100 min-h-[500px]">
                @if($generate->status == 'NEW' || $generate->status == 'IN_QUEUE' || $generate->status == 'IN_PROGRESS')
                    <div class="loading-gif loader_{{$generate->id}} absolute inset-0 flex items-center justify-center">
                        <img width="50" src="{{asset('images/loader.gif')}}" alt="Loading...">
                    </div>
                @endif

                @if(in_array($generate->content_type, $gen->videoTypes()))
                    <video src="{{$generate->video_url}}" 
                           controls 
                           loop 
                           class="w-full h-[500px] object-cover video_{{$generate->id}}"
                           @if($generate->status!=='COMPLETED') style="display:none;" @endif>
                    </video>
                @elseif(in_array($generate->content_type, $gen->imageTypes()))
                    <a href="{{ $generate->image_url ?? '' }}" class="block w-full h-[500px] overflow-hidden">
                        <img src="{{ $generate->image_url ?? asset('images/loading.gif') }}" 
                             class="w-full h-full object-contain image_{{$generate->id}}" 
                             alt="{{$generate->title}}" 
                             loading="lazy">
                                        </a>
                                    @else
                    <div class="flex items-center justify-center h-full text-red-500">
                                        {{ _('Model Undefined') }}
                    </div>
                                    @endif
                                </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap gap-3">
                                    @if(in_array($generate->content_type, $gen->imageTypes()))
                                     @foreach($gen->videoTypes() as $type)
                        @php $fal = App\Models\Fal::where('model_type', $type)->first(); @endphp
                        <a href="{{ route('frontend.generates.createWithParent', ['model_id' => $fal->id, 'image_id' => $generate->id, 'parent_id' => Request::segment(3) ]) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas {{ $fal->icon ?? 'fa-cog' }} mr-2"></i> {{ $fal->title }}
                                     </a>
                                     @endforeach
                                     @endif

                @if(in_array($generate->content_type, $gen->imageTypes()))
                    <a href="{{ $generate->image_url ?? '' }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                       download>
                        <i class="fas fa-download mr-2"></i> Download
                    </a>
                @elseif(in_array($generate->content_type, $gen->videoTypes()))
                    <a href="{{ $generate->video_url ?? '' }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                       download>
                        <i class="fas fa-download mr-2"></i> Download
                    </a>
                @endif
            </div>

            <!-- Status and Info -->
            <div class="mt-6 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Status: <span id="status_{{$generate->id}}" class="ml-1">{{ $generate->status ?? '' }}</span>
                                        </span>
                    <button type="button" 
                            class="refresh-status inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            data-id="{{$generate->id}}">
                        <i class="fas fa-sync-alt mr-2"></i> Refresh
                    </button>
                                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $generate->prompt ?? '' }}</p>
                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm text-gray-500">
                        <p><strong>Created:</strong> {{ $generate->created_at->diffForHumans() ?? '' }}</p>
                        <p><strong>Credits:</strong> {{ $generate->credit ?? '0' }}</p>
                    </div>
                </div>

                @can('generate_delete')
                    <div class="flex justify-end">
                        <form action="{{ route('frontend.generates.destroy', $generate->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                              class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </form>
</div>
                @endcan
</div>
</div>
</div>

    <!-- Child Content Section -->
    <div class="mt-12">
        <h4 class="text-xl font-semibold text-gray-900 mb-6">Generated Variations</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($childs as $child)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden @if($child->status=='NEW' || $child->status=='IN_QUEUE' || $child->status=='IN_PROGRESS') animate-pulse @endif" 
                     data-id="{{ $child->id }}"
                     data-type="{{ $child->content_type }}">
                    <!-- Content Type Badge -->
                    @if(in_array($child->content_type, $gen->videoTypes()))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 m-4">
                            <i class="fas fa-video mr-2"></i> {{ $child->fal->model_name}}
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 m-4">
                            <i class="fas fa-photo mr-2"></i> {{ $child->fal->model_name}}
                        </span>
                    @endif

                    <!-- Media Container -->
                    <div class="relative rounded-lg overflow-hidden bg-gray-100 mx-4">
                        @if($child->status == 'NEW' || $child->status == 'IN_QUEUE' || $child->status == 'IN_PROGRESS')
                            <div class="loading-gif loader_{{$child->id}} absolute inset-0 flex items-center justify-center">
                                <img width="50" src="{{asset('images/loader.gif')}}" alt="Loading...">
</div>
                        @endif

                                    @if(in_array($child->content_type, $gen->videoTypes()))
                            <video src="{{$child->video_url}}" 
                                   controls 
                                   loop 
                                   class="w-full h-[300px] object-cover video_{{ $child->id }}"
                                   @if($child->status!=='COMPLETED') style="display:none;" @endif>
                            </video>
                        @else
                            <a href="{{ $child->image_url ?? '' }}" class="block w-full h-[300px] overflow-hidden">
                                <img src="{{ $child->image_url ?? asset('images/loading.gif') }}" 
                                     class="w-full h-full object-cover image_{{$child->id}}" 
                                     alt="{{$child->title}}" 
                                     loading="lazy">
                            </a>
                                        @endif
                                    </div>

                    <!-- Action Buttons -->
                    <div class="p-4 space-y-4">
                        <div class="flex flex-wrap gap-2">
                                        @if($generate->content_type=='video' || $generate->content_type=='audio' || $generate->content_type=='upscale')
                                        @foreach($gen->imageTypes() as $type)
                                    @php $fal = App\Models\Fal::where('model_type', $type)->first(); @endphp
                                    <a href="{{ route('frontend.generates.createWithParent', ['model_id' => $fal->id, 'image_id' => $child->id, 'parent_id' => $child->parent ]) }}"
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas {{ $fal->icon ?? 'fa-cog' }} mr-1"></i> {{ $fal->title }}
                                        </a>
                                        @endforeach
                                        @endif
                                        
                                        @if(in_array($generate->content_type, $gen->imageTypes()))
                                <a href="{{ $child->video_url ?? '' }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                   download>
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                                        @endif  
                        </div>

                        <!-- Status and Info -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Status: <span id="status_{{$child->id}}" class="ml-1">{{ $child->status ?? '' }}</span>
                                            </span>
                                <button type="button" 
                                        class="refresh-status inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        data-id="{{$child->id}}">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                                </button>
                                    </div>

                            <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                <p class="text-gray-700">{{$child->prompt ?? ''}}</p>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-500">
                                    <p><strong>Created:</strong> {{ $child->created_at->diffForHumans() ?? '' }}</p>
                                    <p><strong>Credits:</strong> {{ $child->credit ?? '0' }}</p>
                                    <p class="col-span-2"><span id="queue_{{$child->id}}">Queue position: {{ $child->queue_position ?? 'Waiting...' }}</span></p>
                                </div>
                            </div>

                            @can('generate_delete')
                                <div class="flex justify-end">
                                    <form action="{{ route('frontend.generates.destroy', $child->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
console.log('Script loaded for build.blade.php');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Track active polling intervals
    const activePolling = new Map();
    const POLLING_INTERVAL = 5000; // 5 seconds
    const MAX_POLLING_TIME = 5 * 60 * 1000; // 5 minutes
    const MAX_RETRIES = 3;
    const RETRY_DELAY = 2000; // 2 seconds
    const RATE_LIMIT_DELAY = 1000; // 1 second between requests
    let lastRequestTime = 0;

    // Log all elements with data-id attributes
    const allElementsWithDataId = document.querySelectorAll('[data-id]');
    console.log('Found elements with data-id:', allElementsWithDataId.length);
    
    // Initial status check for any IN_QUEUE requests
    allElementsWithDataId.forEach(element => {
        const generateId = element.dataset.id;
        const statusElement = document.getElementById('status_' + generateId);
        console.log('Checking element:', generateId, 'Current status:', statusElement?.textContent);
        
        // Check if element is in queue or has media that needs updating
        if (statusElement && (
            ['NEW', 'IN_QUEUE', 'IN_PROGRESS'].includes(statusElement.textContent) ||
            (statusElement.textContent === 'COMPLETED' && !hasValidMedia(element))
        )) {
            console.log('Starting polling for ID:', generateId, 'Status:', statusElement.textContent);
            startPolling(element, generateId);
        }
    });

    function hasValidMedia(element) {
        const generateId = element.dataset.id;
        const videoElement = document.querySelector('.video_' + generateId);
        const imageElement = document.querySelector('.image_' + generateId);
        
        if (videoElement) {
            return videoElement.src && videoElement.src !== window.location.href;
        }
        if (imageElement) {
            return imageElement.src && imageElement.src !== window.location.href;
        }
        return false;
    }

    function startPolling(element, generateId) {
        // Clear any existing polling for this ID
        if (activePolling.has(generateId)) {
            console.log('Clearing existing polling for ID:', generateId);
            clearInterval(activePolling.get(generateId));
        }

        const startTime = Date.now();
        console.log('Starting new polling for ID:', generateId, 'at:', new Date().toISOString());
        
        const intervalId = setInterval(async () => {
            // Check if we've exceeded the maximum polling time
            if (Date.now() - startTime > MAX_POLLING_TIME) {
                console.log('Max polling time reached for ID:', generateId);
                stopPolling(generateId);
                showToast('warning', 'Status check timeout. Please refresh manually.');
                return;
            }

            try {
                console.log('Polling check for ID:', generateId, 'at:', new Date().toISOString());
                await checkStatus(element, generateId, generateId);
                
                // Check if we have valid media and status is COMPLETED
                const statusElement = document.getElementById('status_' + generateId);
                if (statusElement && statusElement.textContent === 'COMPLETED' && hasValidMedia(element)) {
                    console.log('Media loaded successfully for ID:', generateId);
                    stopPolling(generateId);
                } else if (statusElement && ['ERROR'].includes(statusElement.textContent)) {
                    console.log('Stopping polling for ID:', generateId, 'Final status:', statusElement.textContent);
                    stopPolling(generateId);
                }
            } catch (error) {
                console.error('Polling error for ID:', generateId, error);
                stopPolling(generateId);
                showToast('error', 'Error checking status: ' + error.message);
            }
        }, POLLING_INTERVAL);

        activePolling.set(generateId, intervalId);
        console.log('Active polling intervals:', Array.from(activePolling.keys()));
    }

    function stopPolling(generateId) {
        if (activePolling.has(generateId)) {
            clearInterval(activePolling.get(generateId));
            activePolling.delete(generateId);
            console.log('Stopped polling for ID:', generateId);
        }
    }

    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            console.error('CSRF token not found');
            showToast('error', 'CSRF token not found. Please refresh the page.');
            throw new Error('CSRF token not found');
        }
        console.log('CSRF token found');
        return token.content;
    }

    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.innerHTML = `@include('components.toast', ['type' => '${type}', 'message' => '${message}'])`;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-0 right-0 z-50 space-y-4 p-4';
        document.body.appendChild(container);
        return container;
    }

    function showLoading(element) {
        element.classList.add('opacity-50');
        element.setAttribute('data-loading', 'true');
        const spinner = document.createElement('div');
        spinner.innerHTML = `@include('components.loading-spinner', ['size' => 'sm', 'color' => 'indigo'])`;
        element.appendChild(spinner);
    }

    function hideLoading(element) {
        element.classList.remove('opacity-50');
        element.removeAttribute('data-loading');
        const spinner = element.querySelector('.animate-spin');
        if (spinner) spinner.remove();
    }

    async function waitForRateLimit() {
        const now = Date.now();
        const timeSinceLastRequest = now - lastRequestTime;
        if (timeSinceLastRequest < RATE_LIMIT_DELAY) {
            await new Promise(resolve => setTimeout(resolve, RATE_LIMIT_DELAY - timeSinceLastRequest));
        }
        lastRequestTime = Date.now();
    }

    async function fetchWithRetry(url, options, retries = MAX_RETRIES) {
        try {
            await waitForRateLimit();
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            if (retries > 0) {
                console.warn(`Retrying... ${retries} attempts left`);
                await new Promise(resolve => setTimeout(resolve, RETRY_DELAY));
                return fetchWithRetry(url, options, retries - 1);
            }
            throw error;
        }
    }

    async function checkStatus(element, generateId, loaderId) {
        console.log('Checking status for ID:', generateId, 'at:', new Date().toISOString());
        try {
            showLoading(element);
            
            const csrfToken = getCsrfToken();
            await waitForRateLimit();
            
            console.log('Making status request for ID:', generateId);
            const response = await fetch('{{ route('frontend.generates.status') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id: generateId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Status response for ID', generateId, ':', data);

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
                const oldStatus = statusElement.textContent;
                statusElement.textContent = data.status;
                console.log('Status updated for ID:', generateId, 'from:', oldStatus, 'to:', data.status);
            }

            // Handle different status states
            if (data.status === 'COMPLETED') {
                console.log('Generation completed for ID:', generateId);
                handleCompletedStatus(element, generateId, data);
                
                // If media is not yet loaded, keep polling
                if (!hasValidMedia(element)) {
                    console.log('Media not yet loaded for ID:', generateId, 'continuing to poll');
                return;
                }
            } else if (data.status === 'ERROR') {
                console.log('Generation error for ID:', generateId);
                handleErrorStatus(element, generateId, data);
            } else if (['NEW', 'IN_QUEUE', 'PROCESSING'].includes(data.status)) {
                console.log('Generation in progress for ID:', generateId, 'Status:', data.status);
                handleInProgressStatus(element, generateId);
            } else {
                console.log('Unknown status:', data.status, 'for ID:', generateId);
                showToast('warning', 'Unknown status: ' + data.status);
            }
        } catch (error) {
            console.error('Error checking status for generate ID:', generateId, error);
            document.getElementById('status_' + generateId).textContent = 'ERROR: ' + error.message;
            element.classList.add('error');
            showToast('error', 'Error checking status: ' + error.message);
            throw error; // Re-throw to handle in polling
        } finally {
            hideLoading(element);
        }
    }

    function handleVideoContent(element, generateId, data) {
        console.log('Handling video content for ID:', generateId, 'Data:', data);
        const videoElement = document.querySelector('.video_' + generateId);
        
        if (!videoElement) {
            console.error('Video element not found for ID:', generateId);
            showToast('error', 'Video element not found');
            return;
        }

        if (!data.video_url) {
            console.error('No video URL provided for ID:', generateId);
            showToast('error', 'No video URL available');
            return;
        }

        const timestamp = new Date().getTime();
        const videoUrl = data.video_url + "?t=" + timestamp;
        console.log('Loading video URL:', videoUrl);
        
        // Hide any image elements
        const imageElement = document.querySelector('.image_' + generateId);
        if (imageElement) {
            imageElement.style.display = 'none';
        }

        // Show and load video
        videoElement.style.display = 'block';
        videoElement.src = videoUrl;
        
        // Add event listeners for video loading
        videoElement.onloadeddata = () => {
            console.log('Video loaded successfully for ID:', generateId);
            videoElement.play().catch(e => console.log('Auto-play prevented:', e));
        };

        videoElement.onerror = (e) => {
            console.error('Error loading video:', e);
            showToast('error', 'Failed to load video. Please try refreshing the page.');
        };

        // Force video load
        videoElement.load();
        
        // Hide loading indicators
        document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'none');
    }

    function handleCompletedStatus(element, generateId, data) {
        console.log('Generation completed for ID', generateId, 'Data:', data);
        element.classList.remove('waiting');
        document.querySelectorAll('.loader_' + generateId).forEach(el => el.classList.add('hide'));

        // Get the content type from the data or element
        const contentType = data.type || element.dataset.type;
        console.log('Content type for ID', generateId, ':', contentType);

        // Log element existence and URLs
        console.log('Elements and URLs:', {
            contentType: contentType,
            imageUrl: data.image_url,
            videoUrl: data.video_url,
            element: element
        });

        // Check if we have a video URL
        if (data.video_url) {
            console.log('Found video URL, handling video content');
            handleVideoContent(element, generateId, data);
        }
        // Check if we have an image URL
        else if (data.image_url) {
            console.log('Found image URL, handling image content');
            handleImageContent(element, generateId, data);
        }
        else {
            console.error('No media URL found for ID', generateId);
            showToast('error', 'No media URL found');
        }
        showToast('success', 'Generation completed successfully!');
    }

    function handleErrorStatus(element, generateId, data) {
        console.error('Generation error for ID', generateId);
        element.classList.remove('waiting');
        element.classList.add('error');
        document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'none');
        showToast('error', data.error || 'Generation failed. Please try again.');
    }

    function handleInProgressStatus(element, generateId) {
        console.log('Generation in progress for ID:', generateId);
        element.classList.add('waiting');
        document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'block');
    }

    function handleImageContent(element, generateId, data) {
        console.log('Handling image content for ID:', generateId, 'URL:', data.image_url);
        const imageElement = document.querySelector('.image_' + generateId);
        
        if (!imageElement) {
            console.error('Image element not found for ID:', generateId);
            showToast('error', 'Image element not found');
            return;
        }

        if (!data.image_url) {
            console.error('No image URL provided for ID:', generateId);
            showToast('error', 'No image URL available');
            return;
        }

        const timestamp = new Date().getTime();
        const imageUrl = data.image_url + "?t=" + timestamp;
        console.log('Loading image URL:', imageUrl);
        
        // Hide any video elements
        const videoElement = document.querySelector('.video_' + generateId);
        if (videoElement) {
            videoElement.style.display = 'none';
        }

        // Show and load image
        imageElement.style.display = 'block';
        imageElement.src = imageUrl;
        
        // Hide loading indicators
        document.querySelectorAll('.loading-gif, .loader_' + generateId).forEach(el => el.style.display = 'none');

        // Add event listeners for image loading
        imageElement.onload = () => {
            console.log('Image loaded successfully for ID:', generateId);
        };

        imageElement.onerror = (e) => {
            console.error('Error loading image:', e);
            showToast('error', 'Failed to load image. Please try refreshing the page.');
        };
    }

    // Add event listener for manual refresh
    document.querySelectorAll('.refresh-status').forEach(button => {
        button.addEventListener('click', function() {
            const generateId = this.dataset.id;
            const element = document.querySelector(`[data-id="${generateId}"]`);
            if (element) {
                // Get the content type from the element's data attribute
                const contentType = element.dataset.type;
                console.log('Manual refresh for ID:', generateId, 'Content type:', contentType);
                startPolling(element, generateId);
                    }
                });
            });
});
</script>
@endsection
