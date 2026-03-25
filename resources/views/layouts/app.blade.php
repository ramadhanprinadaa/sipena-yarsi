<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        @yield('title')
    </title>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lexend:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="h-screen overflow-hidden">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="flex flex-row h-full" 
     x-data="{ sidebarToggle: $persist(true) }"
    >

    <!-- Sidebar -->
    <aside id="sidebar" :class="sidebarToggle ? 'w-64' : 'w-24'" class="border-r border-gray-300 h-full transition-all duration-300">
        @include('layouts.sidebar')
    </aside>

    <!-- Right Content -->
    <div class="flex-1 min-h-screen h-full overflow-y-auto flex flex-col">

        <!-- Header -->
        <header class="border-b border-gray-300 bg-white sticky top-0 z-10 shrink-0 h-16 p-2">
            @include('layouts.header')
        </header>

        <!-- Main Content -->
        <main class="p-6 bg-gray-100 flex-1 min-h-120">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="">
            @include('layouts.footer')
        </footer>

    </div>

</div>

@stack('scripts')

</body>
</html>