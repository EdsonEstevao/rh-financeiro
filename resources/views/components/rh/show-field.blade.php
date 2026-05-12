{{-- resources/views/components/rh/show-field.blade.php --}}
@props([
    'label' => '',
    'value' => '—',
])

<div {{ $attributes->merge(['class' => '']) }}>
    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
        {{ $label }}
    </dt>
    <dd class="mt-1 text-sm font-medium text-gray-900 break-words">
        {{ $value ?: '—' }}
    </dd>
</div>
