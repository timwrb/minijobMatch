<section class="@if($section->padding_y) py-8 sm:py-16 @endif">
    @foreach($section->blocks as $block)
        @include('statamic.blocks.' . $block->type)
    @endforeach
</section>
