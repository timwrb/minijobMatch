<section>

    <div class="flex justify-center py-16 w-screen h-full bg-white rounded-bl-[64px] sm:rounded-bl-[100px]">

        <div class="flex flex-col gap-16 pt-16 mx-auto w-full max-w-6xl sm:pt-32">

            {{-- H1 Hero Shot --}}
            <div class="px-4 w-full text-2xl font-bold text-left sm:px-0 sm:text-3xl sm:text-center md:text-4xl lg:text-5xl xl:text-left text-zinc-800">
                <h1 class="leading-snug">
                    Entdecke die besten
                    <span class="text-primary">Minijobs</span>
                    <br>
                    oder starte dein
                    <span class="text-secondary">Praktikum</span>
                </h1>
            </div>

            {{-- Search Bar --}}
            <form
                action="/stellenanzeigen"
                method="get"
                class="flex flex-col gap-2 px-4 md:flex-row md:gap-4 md:px-0 lg:max-w-2xl xl:max-w-4xl sm:max-xl:mx-auto"
            >
                <div class="flex flex-col gap-2 w-full sm:flex-row md:gap-4">

                    {{-- SearchTerm --}}
                    <div class="relative w-full md:max-w-[28rem]">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="size-6 text-zinc-500" />
                        </div>
                        <input
                            name="suchbegriff"
                            type="text"
                            placeholder="Suchbegriff"
                            class="py-3 px-6 pl-12 w-full rounded-full border-none transition-all duration-150 focus:ring-transparent bg-zinc-100 hover:bg-zinc-200 focus:border-primary"
                        />
                    </div>


                    <div class="relative w-full md:max-w-[18rem]">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <x-heroicon-o-map-pin class="size-6 text-zinc-500" />
                        </div>
                        <input
                            name="stadt"
                            type="text"
                            placeholder="Ort"
                            class="py-3 px-6 pl-12 w-full rounded-full border-none transition-all duration-150 focus:ring-transparent bg-zinc-100 hover:bg-zinc-200 focus:border-primary"
                        />
                    </div>
                </div>

                <button type="submit" class="flex gap-2 justify-center items-center py-3 px-6 text-base font-semibold text-white rounded-full transition-all duration-150 group bg-primary text-nowrap active:scale-[0.98]">
                    <x-heroicon-o-magnifying-glass class="text-white stroke-2 size-5" />
                    <span>Suchen</span>
                </button>

            </form>

        </div>

    </div>

</section>
