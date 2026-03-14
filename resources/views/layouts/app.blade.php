<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        @yield('title')
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('scripts')
</head>
<body>

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col">
            {{-- Header --}}
            @include('layouts.header')

            {{-- Main Content --}}
            <div class="flex-1 p-6 rounded-lg overflow-y-auto">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('layouts.footer')
        </div>
    
</body>
</html>