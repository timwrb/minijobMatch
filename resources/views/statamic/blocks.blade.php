<x-layouts.app>

    @foreach($section->blocks as $block)
        @include('statamic.blocks.' . $block->type)
    @endforeach

</x-layouts.app>
