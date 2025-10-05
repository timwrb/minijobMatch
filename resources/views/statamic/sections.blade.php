<x-layouts.statamic>
    @foreach($page->section as $section)
        @include('statamic.sections.' . $section->type, ['section' => $section])
    @endforeach
</x-layouts.statamic>
