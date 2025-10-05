<section class="grid grid-cols-1 gap-8 mx-auto w-full max-w-6xl md:grid-cols-2 md:gap-16 xl:px-0 px-[2rem] md:px-[4rem]">

    <div class="flex-col col-span-1 space-y-2 md:mb-[2rem]">
        <x-prose>
            {!! $block->bard !!}
        </x-prose>
    </div>

    <div class="flex flex-col col-span-1 gap-4">
        @foreach($block->faq as $item)
            <div x-data="{ expanded: false }" class="py-2 px-6 mx-auto max-w-3xl bg-white rounded-[2rem]">
                <h2>
                    <button
                        id="{{ $item->id }}"
                        type="button"
                        class="flex justify-between items-center py-2 w-full font-medium text-left"
                        @click="expanded = !expanded"
                        :aria-expanded="expanded"
                        aria-controls="{{ $item->id }}"
                    >
                        <span class="text-gray-700 md:text-lg text-md">{{ $item->question }}</span>
                        <svg class="ml-8 fill-customBlue shrink-0 scale-[1.1]" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
                            <rect y="7" width="16" height="2" rx="1" class="transition duration-200 ease-out transform origin-center" :class="{'!rotate-180': expanded}" />
                            <rect y="7" width="16" height="2" rx="1" class="transition duration-200 ease-out transform origin-center rotate-90" :class="{'!rotate-180': expanded}" />
                        </svg>
                    </button>
                </h2>
                <div
                    id="{{ $item->id }}"
                    role="region"
                    aria-labelledby="{{ $item->id }}"
                    class="grid overflow-hidden text-sm transition-all duration-300 ease-in-out text-slate-600 md:text-md"
                    :class="expanded ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                >
                    <div class="overflow-hidden">
                        <p class="pb-3">
                            {{ $item->answer }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</section>
