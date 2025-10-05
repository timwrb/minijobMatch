@props(['buttons'])
<div class="flex flex-wrap gap-2 items-center sm:gap-4">
    @foreach($buttons as $button)
        <x-button :button="$button"/>
    @endforeach
</div>
