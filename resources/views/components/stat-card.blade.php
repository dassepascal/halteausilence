@props([
    'title' => '',
    'value' => '',
    'icon' => '',
    'colour' => 'blue'
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 text-blue-800',
        'green' => 'bg-green-100 text-green-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'red' => 'bg-red-100 text-red-800',
        // Ajoute d'autres couleurs si besoin
    ];
@endphp

<div class="flex items-center p-4 rounded-lg shadow {{ $colorClasses[$colour] ?? $colorClasses['blue'] }}">
    <div class="flex-shrink-0 mr-4">
        @if($icon)
            <x-dynamic-component :component="'icon-'.$icon" class="w-8 h-8"/>
        @endif
    </div>
    <div>
        <div class="text-lg font-bold">{{ $value }}</div>
        <div class="text-sm">{{ $title }}</div>
    </div>
</div>
