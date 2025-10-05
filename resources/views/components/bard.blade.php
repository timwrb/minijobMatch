@props(['bard'])

<div>
    @foreach($bard as $set)
        @if($set->type === 'cta_button')
            <x-buttons :buttons="$set->buttons"/>
        @elseif($set->type == 'bullet_points')
            <ul class="flex flex-col gap-2">
                @foreach($set->bullet_points as $bullet)
                    <li class="flex flex-row gap-2">
                        <x-heroicon-o-check-circle class="size-8 text-secondary shrink-0" />
                        <p class="mt-0.5 text-lg font-semibold">{{ $bullet }}</p>
                    </li>
                @endforeach
            </ul>
        @elseif($set->type === 'text')
            @if(is_string($set->text))
                <x-prose>
                    {!! $set->text !!}
                </x-prose>
            @endif
        @endif
    @endforeach
</div>
