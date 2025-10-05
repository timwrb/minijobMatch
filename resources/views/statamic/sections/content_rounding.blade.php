@php
    // This is a variable used to store classes that get built together dynamically, so the tailwind compiler can pick them up and serve them.
    // if these classes are not fully written out, tailwind will not recognize them since the compiler doesn't execute php which would build needed classes.
    $tailwind =
    ['
        rounded-tl-[64px] sm:rounded-tl-[100px]
        rounded-tr-[64px] sm:rounded-tr-[100px]
        rounded-bl-[64px] sm:rounded-bl-[100px]
        rounded-br-[64px] sm:rounded-br-[100px]
    '];

    $roundedClasses = [];
    foreach ($section->roundings as $rounding) {
        $roundedClasses[] = 'sm:rounded-' . $rounding['value'] . '-[100px]';
        $roundedClasses[] = 'rounded-' . $rounding['value'] . '-[64px]';
    }
    $hardRoundedClasses = implode(' ', $roundedClasses);
@endphp

<section class="@if($section->soft_rounded_corners) rounded-2xl @endif
                max-w-{{ $section->width }} w-full mx-auto
                @if($section->width !== 'full') my-8 sm:my-16 @endif
                bg-{{ $section->bg }}
                @if($section->padding_y) px-4 sm:px-8 py-8 sm:py-16 @endif
                {{ $hardRoundedClasses }}
                ">

    {{-- Blocks Replicator --}}
    @foreach($section->blocks as $block)
        @include('statamic.blocks.' . $block->type)
    @endforeach
    {{-- /Blocks Replicator --}}
</section>
