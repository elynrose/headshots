@extends('layouts.frontend')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $scene->title }}</h1>
            <p class="mt-2 text-sm text-gray-600">
                Scene from campaign: 
                <a href="{{ route('frontend.campaigns.show', $scene->campaign_id) }}" 
                   class="text-indigo-600 hover:text-indigo-900">
                    {{ $scene->campaign->title }}
                </a>
            </p>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $scene->status }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Language</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ strtoupper($scene->language) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Media Type</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($scene->media_type) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Created</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $scene->created_at->format('F j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Scene Details</h2>
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Character Description</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $scene->character_description }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Background Description</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $scene->background_description }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Character Actions</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $scene->character_actions }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Voice Over</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $scene->voice_over }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Camera Angle</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $scene->camera_angle }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Zoom Level</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $scene->zoom_level }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('frontend.scenes.edit', $scene->id) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit Scene
            </a>
            <a href="{{ route('frontend.campaigns.show', $scene->campaign_id) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Back to Campaign
            </a>
        </div>
    </div>
</div>
@endsection 