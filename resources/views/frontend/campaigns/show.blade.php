@extends('layouts.frontend')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $campaign->title }}</h1>
        <p class="mt-2 text-sm text-gray-600">Created {{ $campaign->created_at->diffForHumans() }}</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Campaign Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <p class="mt-1 text-sm text-gray-900">{{ $campaign->status }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Prompt</h3>
                <p class="mt-1 text-sm text-gray-900">{{ $campaign->prompt }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Scenes</h2>
            @if($campaign->status === 'NEW')
                <div class="text-sm text-gray-500">
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></span>
                    Generating scenes...
                </div>
            @endif
        </div>

        @if($campaign->scenes->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500">No scenes generated yet.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($campaign->scenes as $scene)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium">{{ $scene->title }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($scene->status === 'COMPLETED') bg-green-100 text-green-800
                                @elseif($scene->status === 'IN_PROGRESS') bg-blue-100 text-blue-800
                                @elseif($scene->status === 'ERROR') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $scene->status }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <h4 class="font-medium text-gray-500">Character</h4>
                                <p class="mt-1">{{ $scene->character_description }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-500">Background</h4>
                                <p class="mt-1">{{ $scene->background_description }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-500">Actions</h4>
                                <p class="mt-1">{{ $scene->character_actions }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-500">Voice Over</h4>
                                <p class="mt-1">{{ $scene->voice_over }}</p>
                            </div>
                        </div>

                        @if($scene->status === 'COMPLETED')
                            <div class="mt-4 flex space-x-4">
                                <a href="{{ route('frontend.scenes.edit', $scene->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Edit Scene
                                </a>
                                <a href="{{ route('frontend.scenes.show', $scene->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                    View Details
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-8 flex justify-between">
        <a href="{{ route('frontend.campaigns.edit', $campaign->id) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Edit Campaign
        </a>
        <a href="{{ route('frontend.campaigns.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Back to Campaigns
        </a>
    </div>
</div>
@endsection 