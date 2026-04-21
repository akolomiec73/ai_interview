<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        @vite(['resources/css/app.css'])
        @stack('styles')
        @stack('scripts')
    </head>
    <body>
        @yield('content')
    </body>
</html>
