@props(['trigger' => 'Open Drawer'])

<div x-data="{ drawerOpen: false }" class="relative">
    <!-- Trigger button -->
    <div @click="drawerOpen = true">
        {{ $trigger }}
    </div>

    <!-- Drawer backdrop -->
    <div x-show="drawerOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black bg-opacity-50"
         @click="drawerOpen = false">
    </div>

    <!-- Drawer content -->
    <div x-show="drawerOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-y-full"
         x-transition:enter-end="transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-y-0"
         x-transition:leave-end="transform translate-y-full"
         class="fixed inset-x-0 bottom-0 z-50 w-full shadow-lg h-[80vh] bg-almostWhite rounded-t-[2rem]">

        <div class="flex flex-col p-8 h-full">
            <!-- Close button -->
            <div class="mt-auto">
                <button @click="drawerOpen = false"
                        class="py-3 w-full font-semibold text-center text-white rounded-xl transition-all duration-150 bg-customBlue active:scale-[0.95]">
                    Schließen
                </button>
            </div>

            <!-- Scrollable content area -->
            <div class="overflow-y-auto flex-grow">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
