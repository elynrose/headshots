@props(['size' => 'md', 'color' => 'indigo'])

@php
$sizeClasses = [
    'sm' => 'w-4 h-4',
    'md' => 'w-8 h-8',
    'lg' => 'w-12 h-12'
];

$colorClasses = [
    'indigo' => 'text-indigo-600',
    'white' => 'text-white',
    'gray' => 'text-gray-600'
];

$size = in_array($size, array_keys($sizeClasses)) ? $size : 'md';
$color = in_array($color, array_keys($colorClasses)) ? $color : 'indigo';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    <svg class="animate-spin {{ $sizeClasses[$size] }} {{ $colorClasses[$color] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div> 