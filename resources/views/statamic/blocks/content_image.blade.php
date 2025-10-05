<section class="mx-auto max-w-6xl">
    <div class="flex gap-8
                @if($block->position == 'left') flex-col md:flex-row-reverse @endif
                @if($block->position == 'right') flex-col md:flex-row @endif
                ">

        <div class="flex w-full md:w-1/2">
            @if(! isset($block->image[1]))
                <img
                    src="{{ Statamic::tag('glide')->src($block->image[0])->format('webp')->width(600)->height(400)->dpr(2)->quality(90)->fetch() }}"
                    alt="{{ $block->image[0]->alt }}"
                    class="object-contain rounded-2xl"
                    width="600"
                    height="400"
                />
            @endif
        </div>

        <div class="flex w-full md:w-1/2">
            <x-bard :bard="$block->bard" />
        </div>

    </div>
</section>
