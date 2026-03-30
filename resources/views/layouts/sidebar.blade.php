<div class="flex flex-col h-screen">

    <!-- Logo -->
    <div 
        :class="sidebarToggle ? 'justify-start gap-2 px-2' : 'justify-center'"
        class="flex items-center bg-white p-2 h-16 w-full border-b border-gray-300 cursor-pointer"
    >
        <img src="{{ asset('favicon.ico') }}" class="w-10 h-10 object-contain">

        <div x-show="sidebarToggle" x-transition class="flex flex-col leading-tight">
            <span class="text-gray-600 font-bold">SIPENA</span>
            <span class="text-gray-500 text-xs">
                Sistem Informasi Pegawai dan Administrasi YARSI
            </span>
        </div>
    </div>

    <!-- Container -->
    <div class="flex flex-col flex-1 bg-white p-2">

        <!-- MENU -->
        <div class="space-y-2">

            <!-- LABEL -->
            <span 
                :class="sidebarToggle ? 'text-left text-sm px-2' : 'text-center text-xs'"
                class="block text-gray-600 font-bold uppercase w-full">
                Beranda
            </span>

            <!-- MENU ITEM -->
            @php
                $menuItems = [
                    ['route' => 'kepegawaian', 'icon' => 'fa-address-card', 'label' => 'Kepegawaian'],
                    ['route' => 'presensi', 'icon' => 'fa-user-check', 'label' => 'Presensi'],
                ];
                if (Auth::user()->role->name === 'Super Admin'|| Auth::user()->role->name === 'Pegawai Tendik' || Auth::user()->role->name === 'Admin' || Auth::user()->role->name === 'SDM Universitas') {
                    $menuItems[] = ['route' => 'lembur', 'icon' => 'fa-business-time', 'label' => 'Lembur'];
                }
                $menuItems[] = ['route' => 'cuti', 'icon' => 'fa-plane-departure', 'label' => 'Cuti'];
            @endphp

            @foreach($menuItems as $item)
                <a href="{{ route($item['route']) }}"
                :class="sidebarToggle ? 'justify-start gap-2' : 'justify-center'"
                class="flex items-center w-full p-2 rounded-md transition
                {{ request()->routeIs($item['route']) ? 'bg-cyan-100' : 'hover:bg-cyan-100' }}"
                data-tippy-content="{{ $item['label'] }}"
                >
                    <i class="fa-solid {{ $item['icon'] }} w-7 text-xl text-gray-700"></i>
                    <span x-show="sidebarToggle" x-transition>{{ $item['label'] }}</span>
                </a>
            @endforeach

            <!-- MODUL -->
            @php
                $isModul = request()->routeIs('modul.*');
            @endphp

            <div 
                x-data="{ open: {{ $isModul ? 'true' : 'false' }}, flyout: false }"
                class="relative flex flex-col w-full"
            >

                <!-- Parent -->
                <button 
                    @click="sidebarToggle ? open = !open : flyout = !flyout"
                    :class=" [sidebarToggle ? 'justify-start gap-2' : 'justify-center {{ $isModul ? 'bg-cyan-100' : '' }}', open ? '' : '{{ $isModul ? 'bg-cyan-100' : '' }}']"
                    class="flex items-center w-full p-2 rounded-md hover:bg-cyan-100 transition cursor-pointer"
                    data-tippy-content="Modul"
                >
                    <i class="fa-solid fa-folder w-7 text-xl text-gray-700"></i>
                    <span x-show="sidebarToggle" x-transition>Modul</span>
                    <svg 
                        x-show="sidebarToggle"
                        :class="open ? 'rotate-0' : '-rotate-90'"
                        class="w-5 h-5 ml-auto transition-transform duration-200"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="#6B7280"> 
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div 
                    x-show="open && sidebarToggle"
                    x-transition
                    class="ml-9 mt-1 space-y-1"
                >
                    <a href="{{ route('modul.surat-perintah-lembur') }}"
                       class="block px-2 py-1 rounded transition
                       {{ request()->routeIs('modul.surat-perintah-lembur') ? 'bg-cyan-100' : 'hover:bg-cyan-100' }}">
                        Surat Perintah Lembur
                    </a>
                </div>

                <!-- Flyout -->
                <div 
                    x-show="flyout && !sidebarToggle"
                    @click.outside="flyout = false"
                    x-transition
                    class="relative left-full top-0 ml-2 w-56 bg-white border border-gray-300 rounded-md shadow-lg p-2 z-50"
                >
                    <a href="{{ route('modul.surat-perintah-lembur') }}"
                       class="block px-2 py-2 rounded transition
                       {{ request()->routeIs('modul.surat-perintah-lembur') ? 'bg-cyan-100' : 'hover:bg-cyan-100' }}">
                        Surat Perintah Lembur
                    </a>
                </div>

            </div>

        </div>

        <!-- AKUN -->
        <div class="space-y-2 mt-auto mb-1">

            <span 
                :class="sidebarToggle ? 'text-left text-sm px-2' : 'text-center text-xs'"
                class="block text-gray-600 font-bold uppercase w-full">
                Akun Saya
            </span>

            <div 
                :class="sidebarToggle ? 'gap-2 justify-start' : 'justify-center'"
                class="flex items-center p-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition cursor-pointer"
            >
                <i class="fa-solid fa-user text-gray-600 w-7 text-center text-2xl"></i>

                <div x-show="sidebarToggle" x-transition class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-700">
                        {{ auth()->user()->username }}
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ auth()->user()->role->name }}
                    </span>
                </div>
            </div>

            {{-- Logout --}}
            <form action="{{ route('auth.handle.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    :class="sidebarToggle ? 'gap-2 justify-start' : 'justify-center'"
                    class="flex items-center p-2 bg-red-100 hover:bg-red-200 rounded-md transition cursor-pointer w-full"
                >
                    <i class="fa-solid fa-right-from-bracket w-7 text-red-500 text-xl"></i>
                    <span x-show="sidebarToggle" x-transition class="text-red-500">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>