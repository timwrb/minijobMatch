<x-layouts.app>
    <section class="px-4 pt-32 mx-auto max-w-5xl md:pt-48">

        <div class="flex flex-row justify-between items-center w-full">
             <h1 class="text-5xl font-bold">{{ $page->title }}</h1>
            <div class="flex">
                <p>Stand: {{ Carbon\Carbon::parse($page->updated_at)->format('d.m.Y') }}</p>
            </div>
        </div>

        <section class="my-12">
            <x-bard :bard="$page->bard"/>
        </section>

    </section>
</x-layouts.app>
