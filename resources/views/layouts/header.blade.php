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
                class="bg-white/70 backdrop-blur-sm placeholder:text-gray-500 text-gray-700 border border-gray-300 focus:outline-none focus:ring-1 focus:ring-pink-300 rounded-full py-2 px-4"
            >
        </div>

        {{-- Notification --}}
        <button class="relative flex items-center justify-center cursor-pointer w-10 h-10 bg-gradient-to-br from-blue-300 to-pink-200 rounded-full transition">
            <i class="fa-solid fa-bell text-gray-600"></i>
            {{-- Badge --}}
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full">
                3
            </span>
        </button>

        {{-- User Profile --}}
        <div class="relative" x-data="{ dropdownOpen: false }">
            <!-- Avatar -->
            <button 
                @click="dropdownOpen = !dropdownOpen"
                class="w-10 h-10 flex items-center justify-center 
                    rounded-full cursor-pointer
                    bg-gradient-to-br from-blue-300 to-pink-200 
                    text-gray-700 shadow-sm hover:shadow-md 
                    transition"
            >
                <i class="fa-solid fa-user text-lg"></i>
            </button>
            <div 
                x-show="dropdownOpen"
                x-transition
                @click.outside="dropdownOpen = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
            >
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pengaturan</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keluar</a>
            </div>
        </div>
    </div>
</div>