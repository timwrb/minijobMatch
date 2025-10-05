<section class="flex flex-col gap-12 mx-auto max-w-6xl md:flex-row">

    <div class="flex flex-col gap-4 max-w-[18rem] shrink-0">
        <x-prose>
            <h2>Informiere dich in unsrem <span class="font-bold text-primary">Blog</span></h2>
            {{--<p>Hier wirst du findest du deine Antwort auf deine Fragen im Bezug auf Minijobs, Werkstudenten Stellen oder dein nächstes Praktikum, ganz egal ob Uni oder Schulpraktikum</p>--}}
        </x-prose>

        <div class="flex flex-row gap-4">
            <button class="flex justify-center items-center bg-white rounded-full border-2 border-transparent shadow transition-all duration-150 cursor-pointer size-16 hover:border-primary active:scale-[0.98]">
                <x-heroicon-o-arrow-left class="size-8 text-primary" />
            </button>
            <button class="flex justify-center items-center bg-white rounded-full border-2 border-transparent shadow transition-all duration-150 cursor-pointer size-16 hover:border-primary active:scale-[0.98]">
                <x-heroicon-o-arrow-right class="size-8 text-primary" />
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        @for($i = 1; $i <=2; $i++)
        <div class="col-span-1">
            <div class="flex relative flex-col p-6 bg-white rounded-2xl border-2 border-transparent shadow transition-all duration-150 group hover:border-primary">

                <a class="absolute inset-0 z-10 w-full h-full cursor-pointer" href="#"></a>

                <x-prose>
                    <h3 class="group-hover:underline">Worauf müssen Studenten als Werkstudent achten?</h3>
                </x-prose>

                <p class="mt-2 text-sm line-clamp-2 text-zinc-500">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos eum mollitia nisi, odio quia vel voluptatum! A consequuntur cumque debitis est illo ipsam nihil perferendis praesentium quae repellendus, vel voluptatem.</p>

                <div class="flex flex-row gap-3 items-center mt-4">
                    <div class="rounded-full ring-2 ring-offset-2 size-10 bg-zinc-200 ring-primary"></div>
                    <div class="flex flex-col text-sm">
                        <p class="font-semibold">Tim Wurmbrand</p>
                        <p class="text-xs">25.03.2025</p>
                    </div>
                </div>
            </div>
        </div>

        @endfor


{{--        @for($i = 1; $i <=3; $i++)
            <div class="h-24 rounded-2xl shadow bg-primary">

            </div>

            <button class="flex py-3 px-6 text-base font-semibold rounded-full border-2 border-primary text-primary">Jetzt entdecken</button>

        @endfor--}}
    </div>
</section>
