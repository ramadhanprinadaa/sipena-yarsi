<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield ('title')</title>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lexend:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @vite (['resources/css/app.css', 'resources/js/app.js'])
    @stack ('styles')
    @livewireStyles
</head>

<body class="bg-gradient-to-br from-[#62A6FF] via-[#E4B4FF] to-[#D8E9FF]" x-data="{ sidebarToggle: $persist(true) }">

    {{-- Header --}}
    <header class=" flex items-center fixed top-0 left-0 right-0 z-50 h-18 m-3 p-4 bg-white/75 backdrop-blur-sm shadow-md rounded-xl">
        @include('layouts.header')
    </header>

    <div class="flex px-4 gap-4">

        {{-- Sidebar --}}
        <aside :class="sidebarToggle ? 'w-60 p-3' : 'w-24 p-2'" class="fixed top-24 z-40 left-3 bottom-4 bg-white/75 backdrop-blur-sm shadow-md rounded-xl no-scrollbar">
            @include('layouts.sidebar')
        </aside>

        {{-- Right Content --}}
        <div :class="sidebarToggle ? 'ml-64' : 'ml-26'" class="flex-1 flex flex-col gap-4 min-w-0 overflow-x-hidden">

            {{-- Section --}}
            <div class="flex items-center gap-2 fixed top-24 z-30">
                {{-- Button Toggle Sidebar --}}
                <button
                    @click="sidebarToggle = !sidebarToggle"
                    class="p-1 w-8 h-8 rounded-xl bg-white/70 backdrop-blur-xs shadow-md hover:bg-gray-100 transition cursor-pointer">
                    <i class="fa-solid fa-bars"></i>
                </button>

                {{-- Breadcrumb --}}
                <div class="flex bg-white/70 backdrop-blur-xs shadow-md rounded-xl px-6 items-center h-8 text-sm w-fit max-w-full">
                    @yield('breadcrumb')
                </div>

            </div>

            @php
                $currentRoute = Route::currentRouteName();
                $userRole = auth()->user()->role->name ?? null;
                $isCutiPage = $currentRoute === 'cuti';
                $isCutiRole = in_array($userRole, ['Staff', 'Tendik', 'Dosen']);
                $removeMtFromMain = $isCutiPage && $isCutiRole;
            @endphp

            {{-- Cards Cuti Section --}}
            @if ($removeMtFromMain)
            <div class="{{ $removeMtFromMain ? 'mt-23' : '' }}">
                @yield('CardsCuti')
            </div>
            @endif

            {{-- Main Content --}}
            <main class="bg-white/20 backdrop-blur-sm shadow-md rounded-xl p-6 min-h-[calc(100vh-157px)] mt-35 overflow-x-auto">
                @yield('content')
            </main>

            {{-- Form Cards Section --}}
            @yield('formCards')

            {{-- Footer --}}
            <footer class="backdrop-blur-sm shadow-md rounded-xl mb-4 p-6 bg-black/80 text-gray-300">
                @include('layouts.footer')
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>