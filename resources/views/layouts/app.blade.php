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
</head>

<body class="h-screen overflow-hidden">

<div class="flex flex-row h-full">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 border-r border-gray-300 h-full transition-all duration-300">
        @include('layouts.sidebar')
    </aside>

    <!-- Right Content -->
    <div class="flex-1 min-h-screen h-full overflow-y-auto flex flex-col">

        <!-- Header -->
        <header class="border-b border-gray-300 sticky top-0 z-10 shrink-0 h-16 p-2">
            @include('layouts.navbar')
        </header>

        <!-- Main Content -->
        <main class="p-6 bg-gray-100 flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-cyan-200 p-6 shrink-0">
            @include('layouts.footer')
        </footer>

    </div>

</div>

@stack('scripts')

</body>
</html>