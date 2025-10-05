@php use Filament\Facades\Filament; @endphp
<div class="relative isolate z-[200]">
    <nav
        x-data="{
            navbarAlwaysWhite: false,
            scrolled: false,
            isOpen: false
        }"
        @scroll.window="scrolled = window.scrollY > 0"
        class="fixed w-full border transition-all duration-300"
        :class="{
            'bg-white text-black border-zinc-200': scrolled || navbarAlwaysWhite,
            'bg-transparent border-transparent text-white': !scrolled && !navbarAlwaysWhite
        }"
    >
        {{-- Banner --}}
        <div class="w-full h-8 ring bg-zinc-100 ring-zinc-100">
            <div class="flex flex-row gap-2 justify-center items-center mx-auto max-w-6xl h-full text-xs font-semibold sm:text-sm text-zinc-800">
                <span>minijobMatch befindet sich momentan noch im Aufbau.</span>
                <x-heroicon-s-cog-8-tooth class="animate-spin size-4 text-zinc-950" />
            </div>
        </div>


        <div class="flex justify-center">
            <div class="w-full md:w-[80%] lg:max-w-6xl">
                <div class="flex flex-row justify-between items-center h-[80px]">
                    <a href="/" class="ml-6 cursor-pointer select-none">
                       @include('components.logo')
                        <span class="sr-only">Link zur Startseite</span>
                    </a>

                    {{-- Nav Contents --}}
                    <div class="flex flex-row">
                        <div class="hidden lg:flex">
                            <nav class="flex flex-row justify-center items-center text-base font-semibold text-zinc-950">
                                <s:nav:navbar>
                                    @if($not_available->value() === true)
                                        <div class="relative">
                                            <button disabled class="py-3 px-6">{{ $title }}</button>
                                            <span class="flex absolute inset-0 justify-center items-end text-[0.5rem] text-zinc-400">Bald verfügbar</span>
                                        </div>
                                    @else
                                        <a href="{{ $url }}" class="py-3 px-6">{{ $title }}</a>
                                    @endif
                                </s:nav:navbar>
                            </nav>
                        </div>

                        <div class="hidden my-auto mx-4 h-6 rounded-full lg:flex w-[1px] bg-zinc-300"></div>


                        {{-- Profile Nav & Mobile Drawer --}}
                        <nav class="gap-4">
                            <div class="hidden lg:flex">
                                @auth
                                    <div x-data="{ open: false }" class="text-sm font-medium">

                                        <nav
                                            class="flex relative items-center py-2 px-3 space-x-1 rounded-lg cursor-pointer hover:bg-gray-100"
                                            x-data="{ open: false }"
                                            @mouseenter="open = true"
                                            @mouseleave="open = false"
                                        >
                                            <div class="mr-2 avatar">
                                                <div class="w-8 rounded-full ring-2 ring-offset-2 ring-customBlue">
                                                </div>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</span>

                                            <button
                                                class="p-1 shrink-0"
                                                :aria-expanded="open"
                                                @click.prevent="open = !open"
                                            >
                                                <svg class="w-3 h-3 fill-gray-500" xmlns="http://www.w3.org/2000/svg" width="12" height="12">
                                                    <path d="M10 2.586 11.414 4 6 9.414.586 4 2 2.586l4 4z" />
                                                </svg>
                                            </button>
                                            <!-- 2nd level menu -->
                                            <ul
                                                class="origin-top-right absolute top-full left-1/2 -translate-x-1/2 min-w-[240px] bg-white border border-slate-200 p-2 rounded-lg shadow-xl [&[x-cloak]]:hidden"
                                                x-show="open"
                                                x-transition:enter="transition ease-out duration-200 transform"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-out duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                x-cloak
                                                @focusout="await $nextTick();!$el.contains($focus.focused()) && (open = false)"
                                            >
                                                <li>
                                                    <a class="flex items-center p-2 text-slate-800 hover:bg-slate-50" href="/profil">
                                                        <span class="whitespace-nowrap">Profil</span>
                                                    </a>
                                                </li>
                                               {{-- <li>
                                                    <a class="flex items-center p-2 text-slate-800 hover:bg-slate-50" href="#">
                                                        <span class="whitespace-nowrap">Gespeicherte Jobs</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center p-2 text-slate-800 hover:bg-slate-50" href="#">
                                                        <span class="whitespace-nowrap">Bewerbungen</span>
                                                    </a>
                                                </li>--}}
                                                <!-- divider -->
                                                <li class="my-2 mx-2 border-t border-gray-300"></li>

                                                <li>
                                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                                        @csrf
                                                        <a href="{{ route('logout') }}"
                                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                                           class="flex items-center p-2 text-slate-800 hover:bg-slate-50">
                                                            Abmelden
                                                        </a>
                                                    </form>
                                                </li>

                                                <!-- divider -->
                                                <li class="my-2 mx-2 border-t border-gray-300"></li>

                                                @php
                                                $userTenants = []
                                                    // $userTenants = Filament::getUserTenants(auth()->user());
                                                @endphp


                                                @if($userTenants === [])
                                                    <li>
                                                        <a class="flex items-center p-2 text-slate-800 hover:bg-slate-50" href="/company/new">
                                                            <span class="whitespace-nowrap">Unternehmen registrieren</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <ul class="flex flex-col">
                                                        @foreach($userTenants as $tenant)
                                                            <li class="flex relative flex-row gap-2 items-center p-2 hover:bg-slate-50">
                                                                <a href="/company/{{ $tenant->id }}" class="absolute inset-0"></a>
                                                                <img src="{{ $tenant->logo }}" alt="{{ $tenant->name }}" class="mr-2 rounded-full ring-1 ring-offset-1 size-6 ring-primary">
                                                                <span class="text-zinc-950">{{ $tenant->name }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </ul>
                                        </nav>

                                    </div>
                                @endauth
                                @guest
                                    <div class="flex flex-row gap-2">
                                        <a href="/login" as="button" class="flex justify-center items-center py-3 px-6 w-full text-base font-semibold bg-transparent rounded-full transition-all duration-150 group text-zinc-950 hover:bg-zinc-300 active:scale-[0.98]">Anmelden</a>
                                        <a href="#" as="button" class="flex justify-center items-center py-3 px-6 w-full text-base font-semibold text-white rounded-full transition-all duration-150 group bg-primary text-nowrap active:scale-[0.98]">Für Arbeitgeber</a>
                                    </div>
                                @endguest
                            </div>

                            <div>
                                <x-mjm-ui-kit.drawer>
                                    <x-slot name="trigger">
                                        <button class="flex items-center p-2 mr-4 ml-2 rounded-full lg:hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-zinc-950">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <ul
                                        class="py-4 mt-2 w-full text-medium text-[1.2rem] text-darkGrey100"
                                    >
                                        <s:nav:navbar>
                                            <li class="relative">
                                                @if($not_available->value() === true)
                                                    <div class="flex flex-row gap-2 items-center p-2 my-5">
                                                        <x-heroicon-o-arrow-right class="size-6" />
                                                        <span class="text-darkGrey100/60">{{ $title }}</span>
                                                        <span class="text-xs text-darkGrey100/40">Bald verfügbar</span>
                                                    </div>
                                                @else
                                                    <a href="{{ $url }}" class="flex flex-row gap-2 p-2 my-5">
                                                        <x-heroicon-o-arrow-right class="size-6" />
                                                        {{ $title }}
                                                    </a>
                                                @endif
                                            </li>
                                        </s:nav:navbar>

                                        <li><div class="my-5 mt-2 w-full border-t border-gray-200 rounded-[0rem]"></div></li>

                                        @auth
                                            <li><a href="/login" class="flex flex-row gap-2 p-2 my-5" @click="isOpen = false">
                                                    <x-heroicon-o-user-circle class="size-6" />
                                                    Profil
                                                </a></li>

                                            <li>
                                                <div>
                                                    <a wire:click="logout" class="flex flex-row gap-2 p-2 my-5" @click="isOpen = false">

                                                        <x-heroicon-o-arrow-left-start-on-rectangle class="size-6" />
                                                        Abmelden
                                                    </a>
                                                </div>
                                            </li>
                                        @else
                                            <li><a href="/login" class="flex flex-row gap-2 p-2 my-5" @click="isOpen = false">
                                                    <x-heroicon-o-user-circle class="size-6" />
                                                    Anmelden
                                                </a></li>
                                        @endauth

                                        <li><a href="/company/" class="flex flex-row gap-2 p-2 my-3 font-semibold" @click="isOpen = false">
                                                <x-heroicon-o-briefcase class="size-6" />
                                                Für Arbeitgeber
                                            </a></li>
                                    </ul>

                                </x-mjm-ui-kit.drawer>


                            </div>
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </nav>

</div>
