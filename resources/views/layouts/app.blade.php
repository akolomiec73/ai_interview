<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])
        @stack('styles')
        @stack('scripts')
    </head>
    <body>
        <div class="content">
            <div class="content-header">
                @yield('content-header')
            </div>
            @yield('content')
            <div class="content-footer">
                @yield('content-footer')
            </div>
        </div>
        @yield('include-modals')
    </body>
</html>
