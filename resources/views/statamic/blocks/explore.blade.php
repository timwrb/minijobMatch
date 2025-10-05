<div class="grid grid-cols-1 gap-8 p-4 mx-auto max-w-6xl lg:grid-cols-3 rounded-[2rem]">

    <div class="col-span-full mx-auto max-w-3xl text-center">
        <x-prose>
            <h2>Entdecke Stellen in Städten</h2>
            <p>Nutze diese speziell gefilterten Seiten um eine Stelle in genau deiner Region zu finden, so findest du sicher deinen neuen Arbeitgeber</p>
        </x-prose>
    </div>

    <div class="col-span-full">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($block->landing_pages as $link)
                <div class="col-span-1">
                    <div class="relative group flex flex-row gap-4 justify-between items-center px-8 py-2
                                @if($section->bg == 'white') bg-zinc-100
                                @elseif($section->bg == 'zinc-100') bg-white
                                @elseif($section->bg == 'primary') bg-zinc-100
                                @else bg-white @endif
                                rounded-full
                                ">
                        <div class="flex flex-col">
                            <span class="text-xl font-semibold group-hover:underline text-zinc-950">{{ $link->short_title }}</span>
                            <span class="text-sm">Jetzt {{ $link->search_results }} Stellen entdecken </span>
                            <a href="{{ $link->url }}" class="absolute inset-0 z-10 cursor-pointer"></a>
                        </div>
                        <x-heroicon-o-arrow-right class="transition-all duration-300 size-8 text-zinc-950 group-hover:text-primary group-hover:-rotate-[360deg]" />
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
