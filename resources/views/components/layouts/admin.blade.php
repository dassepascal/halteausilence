<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    {{-- TinyMCE local --}}
    <script src="{{ asset('storage/scripts/tinymce.min.js') }}"></script>

    {{-- Styles compilés avec Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire styles --}}
    @livewireStyles

    {{-- Pushed styles (optionnel) --}}
    @stack('styles')
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- MAIN WRAPPER --}}
    <x-main full-width>

        {{-- SIDEBAR --}}
        <x-slot name="sidebar" drawer="main-drawer" collapsible class="bg-base-100">
            <livewire:admin.sidebar />
        </x-slot>

        {{-- MAIN CONTENT --}}
        <x-slot name="content">
            <!-- Drawer toggle for "main-drawer" -->
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>

            {{-- Page content --}}
            {{ $slot }}

            {{-- Composant calendrier, si voulu partout --}}
         
        </x-slot>

    </x-main>

    {{-- Zone de toast/notifications --}}
    <x-toast />

    {{-- Livewire scripts --}}
    @livewireScripts

    {{-- JS spécifiques à certaines pages --}}
    @stack('scripts')
</body>

</html>
