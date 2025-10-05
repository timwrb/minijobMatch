<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-200.woff2') }}" as="font" />
        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-300.woff2') }}" as="font" />
        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-500.woff2') }}" as="font" />
        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-600.woff2') }}" as="font" />
        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-700.woff2') }}" as="font" />
        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-800.woff2') }}" as="font" />
        <link rel="preload" href="{{ Vite::asset('resources/fonts/manrope-v15-latin_latin-ext-regular.woff2') }}" as="font" />

        @vite('resources/css/app.css')
        @vite('resources/js/app.js')

        @livewireStyles
    </head>
    <body class="w-full bg-zinc-100">
        {{-- Content --}}
        @include('components.navbar')
        {{ $slot }}
        @include('components.footer')
        {{-- Content End --}}

    @livewireScripts
    </body>
</html>
