@props([
    'contrast' => false,
    'headings',
    'class'
])
@aware(['bg' => 'white'])

@php
    $colorClasses = '';
    if (in_array($bg, ['primary'])) {
        $colorClasses = 'prose-p:text-indigo-200/65 prose-headings:text-gray-200';
    } else {
        $colorClasses = 'prose-p:text-indigo-200/65 prose-headings:text-gray-200';
    }
@endphp

<div
    {{ $attributes->merge(['as' => 'article'])->class([
            'prose max-w-none',
            'prose-h1:text-4xl prose-h1:md:text-5xl prose-h1:font-bold prose-h1:leading-tight prose-h1:mb-4',
            'prose-h2:mb-0 prose-h2:text-darkGrey100 prose-h2:pb-4 prose-h2:text-3xl prose-h2:font-semibold prose-h2:md:text-4xl',
            'prose-h3:text-2xl prose-h3:font-semibold prose-h3:leading-tight prose-h3:mb-3',
            'prose-h4:text-xl prose-h4:font-semibold prose-h4:leading-tight prose-h4:mb-2',
            'prose-p:text-xl prose-p:font-thin prose-p:min-h-4 prose-p:text-zinc-700 prose-p:mb-4',
            'prose-a:text-indigo-200/65 prose-a:transition prose-a:hover:text-indigo-500 prose-a:no-underline',
            'prose-strong:text-zinc-950 prose-strong:font-medium',
        ]) }}>
    {{ $slot }}
</div>
