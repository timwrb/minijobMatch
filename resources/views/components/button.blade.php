@props(['button'])
<a
    class="px-6 py-3 rounded-full text-base font-semibold
           group w-full transition-all duration-150 active:scale-[0.98]
           bg-{{ $button->bg }} hover:bg-{{ $button->bg }}/85
           @if($button->bg == 'white') text-zinc-950
           @elseif($button->bg == 'zinc-100') text-zinc-950
           @else text-white @endif
           sm:mb-0 sm:w-auto flex justify-center shadow"
    href="{{ $button->link }}"
>
    <span class="relative inline-flex @if($button->icon == 'prefix') flex-row @elseif($button->icon == 'suffix') flex-row-reverse @endif items-center gap-2">
        <x-dynamic-component :component="'heroicon-'. $button->heroicon_string" class="size-5" />
        {{ $button->text }}
    </span>
</a>
