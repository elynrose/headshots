@extends('layouts.frontend')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create New Campaign</h1>
            <p class="mt-2 text-sm text-gray-600">Create a new campaign and generate scenes automatically.</p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('frontend.campaigns.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Campaign Title</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('title') border-red-300 @enderror"
                                   required>
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prompt" class="block text-sm font-medium text-gray-700">Campaign Prompt</label>
                        <div class="mt-1">
                            <textarea name="prompt" 
                                      id="prompt" 
                                      rows="6"
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('prompt') border-red-300 @enderror"
                                      required>{{ old('prompt') }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Describe your campaign in detail. This will be used to generate scenes automatically.
                        </p>
                        @error('prompt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('frontend.campaigns.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Create Campaign
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 