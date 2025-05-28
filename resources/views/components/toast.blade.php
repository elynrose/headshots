@props(['type' => 'info', 'message' => '', 'duration' => 5000])

@php
$typeClasses = [
    'success' => 'bg-green-100 border-green-400 text-green-700',
    'error' => 'bg-red-100 border-red-400 text-red-700',
    'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
    'info' => 'bg-blue-100 border-blue-400 text-blue-700'
];

$iconClasses = [
    'success' => 'fas fa-check-circle',
    'error' => 'fas fa-exclamation-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle'
];

$type = in_array($type, array_keys($typeClasses)) ? $type : 'info';
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-init="setTimeout(() => show = false, {{ $duration }})"
     class="fixed bottom-0 right-0 mb-4 mr-4 z-50">
    <div class="rounded-lg border px-4 py-3 shadow-lg {{ $typeClasses[$type] }}">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="{{ $iconClasses[$type] }}"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ $message }}</p>
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button @click="show = false" class="inline-flex text-current hover:opacity-75 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div> 