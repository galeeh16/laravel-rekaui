<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Applies the saved theme & sidebar state before first paint to avoid a flash. --}}
        <script>
            (() => {
                try {
                    const theme = localStorage.getItem('theme');
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (theme === 'dark' || (theme === null && prefersDark)) {
                        document.documentElement.classList.add('dark');
                    }

                    if (localStorage.getItem('sidebar') === 'collapsed') {
                        document.documentElement.setAttribute('data-sidebar-collapsed', '');
                    }
                } catch (error) {
                    // Storage can be unavailable; fall back to the defaults.
                }
            })();
        </script>

        @fonts

        @routes

        @vite(['resources/css/app.css', 'resources/js/app.js', "resources/js/pages/{$page['component']}.vue"])

        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="min-h-screen bg-canvas font-sans text-ink antialiased">
        <x-inertia::app />
    </body>
</html>
