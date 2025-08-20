@props([
    'label' => '',
    'model' => '',
    'rows' => 5,
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
    <textarea
        rows="{{ $rows }}"
        wire:model="{{ $model }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500']) }}
    ></textarea>
    @error($model)
        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
    @enderror
</div>
