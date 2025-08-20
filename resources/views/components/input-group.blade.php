@props([
    'label' => '',
    'model' => '',
    'type' => 'text',
    'required' => false,
])

<div class="mb-4">
    @if($label)
        <label class="block text-sm font-medium mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <input
        type="{{ $type }}"
        wire:model="{{ $model }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500']) }}
    >
    @error($model)
        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
    @enderror
</div>
