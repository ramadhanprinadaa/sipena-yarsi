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

<div class="grid grid-cols-12 h-full">

    <!-- Sidebar -->
    <aside class="col-span-2 bg-cyan-500 h-full">
        @include('layouts.sidebar')
    </aside>

    <!-- Right Content -->
    <div class="col-span-10 h-full overflow-y-auto flex flex-col">

        <!-- Header -->
        <header class="h-20 px-4 bg-gray-500 sticky top-0 z-10 shrink-0">
            @include('layouts.navbar')
        </header>

        <!-- Main Content -->
        <main class="p-6 bg-gray-100 flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-cyan-600 p-6 shrink-0">
            @include('layouts.footer')
        </footer>

    </div>

</div>

@stack('scripts')

</body>
</html>