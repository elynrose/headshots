@extends('layouts.frontend')

@section('content')
<div class="space-y-6" x-data="training()">
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
                               @input="filterModels()"
                               placeholder="Search models..." 
                               class="form-input pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <select x-model="filter" 
                            @change="filterModels()"
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
                            <tr x-bind:class="{ 'hidden': !isVisible('{{ $train->status }}', '{{ strtolower($train->title) }}') }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="{{ asset('images/default-model.png') }}" 
                                                 alt="{{ $train->title }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $train->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $train->status }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $train->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($train->status === 'ERROR' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
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
function training() {
    return {
        search: '',
        filter: '',
        isVisible(status, title) {
            const matchesSearch = this.search === '' || title.includes(this.search.toLowerCase());
            const matchesFilter = this.filter === '' || status === this.filter;
            return matchesSearch && matchesFilter;
        },
        filterModels() {
            // No need for manual DOM manipulation, Alpine.js will handle it
        }
    }
}
</script>
@endpush
