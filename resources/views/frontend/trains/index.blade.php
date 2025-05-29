@extends('layouts.training')

@push('styles')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('training', () => ({
            search: '',
            filter: '',
            shouldShow(status, title) {
                const matchesSearch = this.search === '' || title.toLowerCase().includes(this.search.toLowerCase());
                const matchesFilter = this.filter === '' || status === this.filter;
                return matchesSearch && matchesFilter;
            }
        }))
    });
</script>

<style>
    .polling-indicator {
        color: #10B981;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="training">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Model Training</h2>
                    <p class="mt-1 text-sm text-gray-500">Train and manage your custom AI models.</p>
                </div>
            @can('train_create')
                    <a href="{{ route('frontend.trains.create') }}" 
                       class="btn-primary">
                        <i class="fas fa-plus mr-2"></i> Start New Training
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Training Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Active Trainings -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cog text-3xl text-primary-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Trainings</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $trains->where('status', 'IN_PROGRESS')->count() }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Trainings -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Trainings</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $trains->where('status', 'COMPLETED')->count() }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Models -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-robot text-3xl text-purple-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Models</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $trains->total() }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training List -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Your Models</h3>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input type="text" 
                               x-model="search" 
                               placeholder="Search models..." 
                               class="form-input pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <select x-model="filter" 
                            class="form-input">
                        <option value="">All Status</option>
                        <option value="IN_PROGRESS">In Progress</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="ERROR">Error</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($trains as $train)
                            <tr x-bind:class="{ 'hidden': !shouldShow('{{ $train->status }}', '{{ strtolower($train->title) }}') }"
                                data-id="{{ $train->id }}"
                                class="@if($train->status=='NEW' || $train->status=='IN_QUEUE' || $train->status=='IN_PROGRESS') waiting animate-pulse @endif">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                        <i class="fas fa-robot text-3xl text-gray-500"></i>

                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $train->title }}</div>
                                            <div class="text-sm text-gray-500 train-status" data-status="{{ $train->status }}">
                                                {{--$train->status --}}
                                                @if(in_array($train->status, ['NEW', 'IN_QUEUE', 'IN_PROGRESS']))
                                                    <span class="polling-indicator ml-2">● Training in progress</span>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium train-status-badge {{ $train->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($train->status === 'ERROR' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}"
                                          data-status="{{ $train->status }}">
                                        {{ $train->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($train->status === 'IN_PROGRESS')
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-primary-600 h-2.5 rounded-full" 
                                                 style="width: {{ $train->progress ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">{{ $train->progress ?? 0 }}%</span>
                                    @elseif($train->status === 'IN_QUEUE' && $train->queue_position)
                                        <span class="text-sm text-gray-500">Queue position: {{ $train->queue_position }}</span>
                                            @else
                                        <span class="text-sm text-gray-500">-</span>
                                            @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $train->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @can('train_show')
                                            <a href="{{ route('frontend.trains.show', $train->id) }}" 
                                               class="text-primary-600 hover:text-primary-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('train_edit')
                                            <a href="{{ route('frontend.trains.edit', $train->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                            @can('train_delete')
                                            <form action="{{ route('frontend.trains.destroy', $train->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>    
                                            @endcan
                                            </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                                        </div>
                             
            @if($trains->hasPages())
                <div class="mt-4">
                    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                        <div class="-mt-px flex w-0 flex-1">
                            @if($trains->onFirstPage())
                                <span class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500">
                                    <i class="fas fa-chevron-left mr-3"></i>
                                    Previous
                                </span>
                            @else
                                <a href="{{ $trains->previousPageUrl() }}" class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                    <i class="fas fa-chevron-left mr-3"></i>
                                    Previous
                                </a>
                            @endif
                            </div>
                        <div class="hidden md:-mt-px md:flex">
                            @foreach($trains->getUrlRange(1, $trains->lastPage()) as $page => $url)
                                @if($page == $trains->currentPage())
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
                            @if($trains->hasMorePages())
                                <a href="{{ $trains->nextPageUrl() }}" class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
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
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing polling', {
        timestamp: new Date().toISOString(),
        url: window.location.href
    });
    
    // Track active polling intervals
    const activePolling = new Map();
    const POLLING_INTERVAL = 5000; // 5 seconds
    const MAX_POLLING_TIME = 5 * 60 * 1000; // 5 minutes
    const RATE_LIMIT_DELAY = 1000; // 1 second between requests
    let lastRequestTime = 0;
    let pollCount = 0;

    // Initial status check for all items
    const trainElements = document.querySelectorAll('[data-id]');
    console.log('Found train elements:', {
        count: trainElements.length,
        elements: Array.from(trainElements).map(el => ({
            id: el.dataset.id,
            status: el.querySelector('.train-status')?.dataset.status,
            hasStatusElement: !!el.querySelector('.train-status')
        }))
    });
    
    trainElements.forEach(element => {
        const trainId = element.dataset.id;
        const statusElement = element.querySelector('.train-status');
        const currentStatus = statusElement?.dataset.status;
        
        console.log('Checking train element:', {
            trainId,
            currentStatus,
            hasStatusElement: !!statusElement,
            timestamp: new Date().toISOString()
        });
        
        // Start polling if item is in progress
        if (statusElement && ['NEW', 'IN_QUEUE', 'IN_PROGRESS'].includes(currentStatus)) {
            console.log('Starting polling for train:', {
                trainId,
                status: currentStatus,
                timestamp: new Date().toISOString()
            });
            startPolling(element, trainId);
        }
    });

    function startPolling(element, trainId) {
        console.log('startPolling called for train:', {
            trainId,
            timestamp: new Date().toISOString(),
            existingPolling: activePolling.has(trainId)
        });
        
        if (activePolling.has(trainId)) {
            console.log('Clearing existing polling for train:', {
                trainId,
                timestamp: new Date().toISOString()
            });
            clearInterval(activePolling.get(trainId));
        }

        const startTime = Date.now();
        const intervalId = setInterval(async () => {
            pollCount++;
            console.log(`Poll #${pollCount} for train ${trainId}`, {
                trainId,
                pollCount,
                elapsedTime: Date.now() - startTime,
                timestamp: new Date().toISOString()
            });
            
            if (Date.now() - startTime > MAX_POLLING_TIME) {
                console.log('Max polling time reached for train:', {
                    trainId,
                    elapsedTime: Date.now() - startTime,
                    maxTime: MAX_POLLING_TIME,
                    timestamp: new Date().toISOString()
                });
                stopPolling(trainId);
                return;
            }

            try {
                await checkStatus(element, trainId);
                
                const statusElement = element.querySelector('.train-status');
                const currentStatus = statusElement?.dataset.status;
                
                if (['COMPLETED', 'ERROR'].includes(currentStatus)) {
                    console.log('Training completed or errored, stopping polling for train:', {
                        trainId,
                        status: currentStatus,
                        timestamp: new Date().toISOString()
                    });
                    stopPolling(trainId);
                }
            } catch (error) {
                console.error('Polling error for train:', {
                    trainId,
                    error: error.message,
                    stack: error.stack,
                    timestamp: new Date().toISOString()
                });
                stopPolling(trainId);
            }
        }, POLLING_INTERVAL);

        activePolling.set(trainId, intervalId);
        console.log('Polling started for train:', {
            trainId,
            intervalId,
            timestamp: new Date().toISOString()
        });
    }

    function stopPolling(trainId) {
        console.log('stopPolling called for train:', {
            trainId,
            timestamp: new Date().toISOString()
        });
        
        if (activePolling.has(trainId)) {
            clearInterval(activePolling.get(trainId));
            activePolling.delete(trainId);
            console.log('Polling stopped for train:', {
                trainId,
                remainingPolls: activePolling.size,
                timestamp: new Date().toISOString()
            });
            
            // Remove polling indicator
            const element = document.querySelector(`[data-id="${trainId}"]`);
            if (element) {
                const indicator = element.querySelector('.polling-indicator');
                if (indicator) {
                    indicator.remove();
                    console.log('Removed polling indicator:', {
                        trainId,
                        timestamp: new Date().toISOString()
                    });
                }
            }
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

    async function checkStatus(element, trainId) {
        try {
            await waitForRateLimit();
            
            console.log('Checking status for training ID:', trainId, 'at', new Date().toISOString());
            
            const response = await fetch('{{ route('frontend.trains.status') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id: trainId })
            });

            if (!response.ok) {
                console.error('HTTP error:', {
                    status: response.status,
                    statusText: response.statusText,
                    trainId: trainId,
                    timestamp: new Date().toISOString()
                });
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Status check response:', {
                trainId: trainId,
                data: data,
                timestamp: new Date().toISOString()
            });

            if (!data) {
                console.error('No response data received:', {
                    trainId: trainId,
                    timestamp: new Date().toISOString()
                });
                throw new Error('No response data received');
            }

            // Update status text
            const statusElement = element.querySelector('.train-status');
            if (statusElement) {
                const oldStatus = statusElement.textContent;
                console.log('Updating status text:', {
                    trainId: trainId,
                    oldStatus: oldStatus,
                    newStatus: data.status,
                    timestamp: new Date().toISOString()
                });
                statusElement.textContent = data.status;
                statusElement.dataset.status = data.status;
                
                // Add or remove polling indicator
                let indicator = statusElement.querySelector('.polling-indicator');
                if (['NEW', 'IN_QUEUE', 'IN_PROGRESS'].includes(data.status)) {
                    if (!indicator) {
                        indicator = document.createElement('span');
                        indicator.className = 'polling-indicator ml-2';
                        indicator.textContent = '●';
                        statusElement.appendChild(indicator);
                        console.log('Added polling indicator:', {
                            trainId: trainId,
                            status: data.status,
                            timestamp: new Date().toISOString()
                        });
                    }
                } else if (indicator) {
                    indicator.remove();
                    console.log('Removed polling indicator:', {
                        trainId: trainId,
                        status: data.status,
                        timestamp: new Date().toISOString()
                    });
                }
            }

            // Update status badge
            const statusBadge = element.querySelector('.train-status-badge');
            if (statusBadge) {
                const oldStatus = statusBadge.textContent;
                console.log('Updating status badge:', {
                    trainId: trainId,
                    oldStatus: oldStatus,
                    newStatus: data.status,
                    timestamp: new Date().toISOString()
                });
                statusBadge.textContent = data.status;
                statusBadge.dataset.status = data.status;
                statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium train-status-badge ' + 
                    (data.status === 'COMPLETED' ? 'bg-green-100 text-green-800' : 
                     data.status === 'ERROR' ? 'bg-red-100 text-red-800' : 
                     'bg-yellow-100 text-yellow-800');
            }

            // Update queue position if available
            if (data.queue_position !== undefined) {
                console.log('Updating queue position:', {
                    trainId: trainId,
                    queuePosition: data.queue_position,
                    timestamp: new Date().toISOString()
                });
                const progressCell = element.querySelector('td:nth-child(3)');
                if (progressCell) {
                    progressCell.innerHTML = `<span class="text-sm text-gray-500">Queue position: ${data.queue_position || 'Waiting...'}</span>`;
                }
            }

            // Update element classes based on status
            if (['COMPLETED', 'ERROR'].includes(data.status)) {
                console.log('Removing loading classes:', {
                    trainId: trainId,
                    status: data.status,
                    timestamp: new Date().toISOString()
                });
                element.classList.remove('waiting', 'animate-pulse');
            }

            // If there's an error in the response, log it
            if (data.error) {
                console.error('Error in status response:', {
                    trainId: trainId,
                    error: data.error,
                    timestamp: new Date().toISOString()
                });
            }

        } catch (error) {
            console.error('Error checking status:', {
                trainId: trainId,
                error: error.message,
                stack: error.stack,
                timestamp: new Date().toISOString()
            });
            throw error;
        }
    }
    });
</script>
@endpush
