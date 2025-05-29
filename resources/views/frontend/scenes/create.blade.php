@extends('layouts.frontend')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Create Scene</h2>
                    <p class="mt-1 text-sm text-gray-500">Create a new scene for your campaign.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('frontend.scenes.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="campaign_id" class="block text-sm font-medium text-gray-700">Campaign</label>
                        <select name="campaign_id" 
                                id="campaign_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                required>
                            <option value="">Select a campaign</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('campaign_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                        <select name="language" 
                                id="language" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                required>
                            <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                            <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                        </select>
                        @error('language')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="character_description" class="block text-sm font-medium text-gray-700">Character Description</label>
                        <textarea name="character_description" 
                                  id="character_description" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                  required>{{ old('character_description') }}</textarea>
                        @error('character_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="background_description" class="block text-sm font-medium text-gray-700">Background Description</label>
                        <textarea name="background_description" 
                                  id="background_description" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                  required>{{ old('background_description') }}</textarea>
                        @error('background_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="character_actions" class="block text-sm font-medium text-gray-700">Character Actions</label>
                        <textarea name="character_actions" 
                                  id="character_actions" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                  required>{{ old('character_actions') }}</textarea>
                        @error('character_actions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="voice_over" class="block text-sm font-medium text-gray-700">Voice Over</label>
                        <textarea name="voice_over" 
                                  id="voice_over" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('voice_over') }}</textarea>
                        @error('voice_over')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="camera_angle" class="block text-sm font-medium text-gray-700">Camera Angle</label>
                        <select name="camera_angle" 
                                id="camera_angle" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select a camera angle</option>
                            <option value="close_up" {{ old('camera_angle') == 'close_up' ? 'selected' : '' }}>Close Up</option>
                            <option value="medium_shot" {{ old('camera_angle') == 'medium_shot' ? 'selected' : '' }}>Medium Shot</option>
                            <option value="wide_shot" {{ old('camera_angle') == 'wide_shot' ? 'selected' : '' }}>Wide Shot</option>
                            <option value="overhead" {{ old('camera_angle') == 'overhead' ? 'selected' : '' }}>Overhead</option>
                        </select>
                        @error('camera_angle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="zoom_level" class="block text-sm font-medium text-gray-700">Zoom Level</label>
                        <select name="zoom_level" 
                                id="zoom_level" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select a zoom level</option>
                            <option value="close" {{ old('zoom_level') == 'close' ? 'selected' : '' }}>Close</option>
                            <option value="medium" {{ old('zoom_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="far" {{ old('zoom_level') == 'far' ? 'selected' : '' }}>Far</option>
                        </select>
                        @error('zoom_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" 
                                id="status" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('frontend.scenes.index') }}" 
                           class="btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="btn-primary">
                            Create Scene
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 