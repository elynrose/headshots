@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Welcome to Headshots</h3>
                <p class="mt-1 text-sm text-gray-500">Generate and manage your AI-powered content.</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Credits Display -->
                <div class="bg-indigo-50 rounded-lg px-4 py-2">
                    <div class="flex items-center">
                        <i class="fas fa-coins text-indigo-600 mr-2"></i>
                        <span class="text-sm font-medium text-indigo-900">
                            {{ App\Models\Credit::where('email', auth()->user()->email)->first()->points ?? 0 }} Credits
                        </span>
                    </div>
                </div>
                @can('generate_create')
                    <a href="{{ route('frontend.generates.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> New Generation
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                    <i class="fas fa-magic text-2xl text-indigo-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Generations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $generates->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-clock text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recent Activity</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $generates->where('created_at', '>=', now()->subDays(7))->count() }}</p>
                    <p class="text-xs text-gray-500">Last 7 days</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-tasks text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">In Queue</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $generates->whereIn('status', ['NEW', 'IN_QUEUE', 'IN_PROGRESS'])->count() }}</p>
                    <p class="text-xs text-gray-500">Active generations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Models Grid -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-semibold text-gray-900">Available Models</h4>
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-sort mr-2"></i> Sort
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($models as $model)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Model Header -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas {{ $model->icon ?? 'fa-cog' }} text-2xl text-indigo-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $model->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $model->model_name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if(in_array($model->model_type, $gen->videoTypes()))
                                    bg-green-100 text-green-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif">
                                <i class="fas {{ in_array($model->model_type, $gen->videoTypes()) ? 'fa-video' : 'fa-photo' }} mr-2"></i>
                                {{ ucfirst($model->model_type) }}
                            </span>
                        </div>
                    </div>

                    <!-- Model Content -->
                    <div class="px-6 pb-4">
                        <p class="text-sm text-gray-600 mb-4">{{ $model->description }}</p>
                        
                        <!-- Model Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">Credits</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $model->credit }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">Queue</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $model->queue_position ?? '0' }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            @can('generate_create')
                                <a href="{{ route('frontend.generates.create', ['model_id' => $model->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-magic mr-2"></i> Generate
                                </a>
                            @endcan
                            @can('model_show')
                                <a href="{{ route('frontend.models.show', $model->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-info-circle mr-2"></i> Details
                                </a>
                            @endcan
                    </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Generations -->
    @if(count($generates) > 0)
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-xl font-semibold text-gray-900">Recent Generations</h4>
                <a href="{{ route('frontend.generates.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-list mr-2"></i> View All
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($generates as $generate)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Media Preview -->
                        <div class="relative h-48 bg-gray-100">
                            @if(in_array($generate->content_type, $gen->videoTypes()))
                                <video src="{{ $generate->video_url }}" 
                                       class="w-full h-full object-cover"
                                       controls></video>
                            @else
                                <img src="{{ $generate->image_url ?? asset('images/placeholder.jpg') }}" 
                                     class="w-full h-full object-cover"
                                     alt="{{ $generate->title }}">
                            @endif
                            <span class="absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if(in_array($generate->content_type, $gen->videoTypes()))
                                    bg-green-100 text-green-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif">
                                <i class="fas {{ in_array($generate->content_type, $gen->videoTypes()) ? 'fa-video' : 'fa-photo' }} mr-1"></i>
                                {{ ucfirst($generate->content_type) }}
                            </span>
                            <span class="absolute top-2 right-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($generate->status === 'COMPLETED')
                                    bg-green-100 text-green-800
                                @elseif($generate->status === 'ERROR')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $generate->status }}
                            </span>
                        </div>

                        <!-- Content Info -->
                        <div class="p-4">
                            <h5 class="text-sm font-medium text-gray-900 truncate">{{ $generate->title }}</h5>
                            <p class="mt-1 text-xs text-gray-500 truncate">{{ $generate->prompt }}</p>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $generate->created_at->diffForHumans() }}
                                </div>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-coins mr-1"></i>
                                    {{ $generate->credit }}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 flex justify-end space-x-2">
                                <a href="{{ route('frontend.generates.build', ['generate_id' => $generate->id, 'model_id' => $generate->fal->id]) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                @can('generate_delete')
                                    <form action="{{ route('frontend.generates.destroy', $generate->id) }}" 
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
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        init() {
            // Initialize any dashboard-specific functionality
        }
    }))
})
</script>
@endpush