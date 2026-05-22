<div class="flex justify-between items-center w-full">

    {{-- Logo --}}
    <div class="flex items-center">
        <img src="{{ asset('favicon.ico') }}" alt="Logo" class="w-12 mr-4">

        <div class="flex flex-col gap-0 max-w-[45%]">
            <span class="font-bold text-lg text-gray-800">SIPENA</span>
            <span class="font-normal text-xs">Sistem Informasi Pegawai dan Administrasi YARSI</span>
        </div>
    </div>

    {{-- Right Content --}}
    <div class="flex items-center gap-2">

        {{-- Search Bar --}}
        <div class="relative">
            <input
                type="text"
                placeholder="Cari..."
                class="bg-white/70 backdrop-blur-sm placeholder:text-gray-500 text-gray-700 border border-gray-300 focus:outline-none focus:ring-1 focus:ring-pink-300 rounded-full py-2 px-4 w-76"
            >
        </div>

        {{-- Notification --}}
        <div class="relative" x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true"
            @mouseleave="dropdownOpen = false">

            <!-- Avatar -->
            <button
                class="w-10 h-10 flex items-center justify-center
                    rounded-full cursor-pointer
                    bg-gradient-to-br from-blue-300 to-pink-200
                    text-gray-700 shadow-sm hover:shadow-md
                    transition">
                <i class="fa-solid fa-bell text-lg"></i>
                <span
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full">
                    3
                </span>
            </button>

            {{-- Dropdown --}}
            <div x-show="dropdownOpen" x-transition
                class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
                <!-- Item -->
                <a href="#" class="flex gap-3 px-3 py-2 hover:bg-gray-50 transition rounded-md">

                    <div class="mt-1">
                        <i class="fa-solid fa-user-plus text-blue-500"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm text-gray-700">
                            User baru <b>Imut Rizman</b> telah ditambahkan
                        </p>
                        <span class="text-xs text-gray-400">
                            2 menit lalu
                        </span>
                    </div>

                    <!-- unread dot -->
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                </a>
                <!-- Item -->
                <a href="#" class="flex gap-3 px-3 py-2 hover:bg-gray-50 transition rounded-md">

                    <div class="mt-1">
                        <i class="fa-solid fa-file-signature text-emerald-500"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm text-gray-700">
                            Pengajuan lembur menunggu persetujuan
                        </p>
                        <span class="text-xs text-gray-400">
                            10 menit lalu
                        </span>
                    </div>

                    <!-- unread dot -->
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                </a>
            </div>

        </div>

        {{-- User Profile --}}
        <div class="relative" x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true"
            @mouseleave="dropdownOpen = false">

            <!-- Avatar -->
            <button
                class="w-10 h-10 rounded-full cursor-pointer
                    bg-gradient-to-br from-blue-300 to-pink-200
                    text-gray-700 shadow-sm hover:shadow-md
                    transition">
                <i class="fa-solid fa-user text-lg"></i>
            </button>
            <div x-show="dropdownOpen" x-transition
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                <a wire:navigate href="{{ route('profile') }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-t-md text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-user"></i>
                    <span>Profil</span>
                </a>

                <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-gear"></i>
                    <span>Pengaturan</span>
                </a>

                {{-- Divider --}}
                <div class="border-b border-gray-200 max-w-[90%] mx-auto"></div>

                <form method="POST" action="{{ route('auth.handle.logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 w-full text-left px-3 py-3 text-sm text-red-600 hover:bg-red-100 cursor-pointer rounded-b-md">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
