@props([
    'label' => '',
    'type' => 'button',
    'rounded' => 'md',
    'iconType' => null,
    'iconName' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
])

@php
    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];

    $roundedClass = $roundedClasses[$rounded] ?? $roundedClasses['md'];
    $widthClass = $fullWidth ? 'w-full' : '';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-4 py-2 {$widthClass} bg-customBlue hover:bg-opacity-85 active:scale-[0.98] transition-all duration-150 {$roundedClass} inline-flex items-center justify-center"]) }}
>
    @if($iconName && $iconType && $iconPosition === 'left')
        <x-dynamic-component :component="'heroicon-' . $iconType . '-' . $iconName" class="mr-2 w-5 h-5" />
    @endif

    {{ $label }}

    @if($iconName && $iconType && $iconPosition === 'right')
        <x-dynamic-component :component="'heroicon-' . $iconType . '-' . $iconName" class="ml-2 w-5 h-5" />
    @endif
</button>
