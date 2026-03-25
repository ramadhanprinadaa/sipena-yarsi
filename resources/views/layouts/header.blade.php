<div class="flex flex-row justify-between items-center w-full">

    {{-- Left Content --}}
    <div class="flex flex-row items-center gap-4">
        {{-- Button Sidebar --}}
        <button @click="sidebarToggle = !sidebarToggle" 
                class="flex items-center justify-center bg-cyan-100 rounded-sm w-12 h-12 cursor-pointer">

            <img src=" {{ asset('icons/sidebar.png') }} " 
                alt="button sidebar" 
                class="w-6 h-6 object-contain">
        </button>

        {{-- Breadcrumb --}}
        <div class="">
            @yield('breadcrumb')
        </div>

    </div>


    {{-- Right Content --}}
    <div class="flex items-center gap-4"">

        {{-- Notification --}}
        <button class="relative flex items-center justify-center cursor-pointer w-10 h-10 bg-cyan-100 rounded-full hover:bg-cyan-200 transition">
            <i class="fa-solid fa-bell text-gray-600"></i>
            {{-- Badge --}}
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full">
                3
            </span>
        </button>

        {{-- User Profile --}}
        <div class="relative" x-data="{ dropdownOpen: false }">

            <img 
                src="{{ asset('icons/icon-user-profile.png') }}" 
                class="w-12 h-12 rounded-full object-cover cursor-pointer"
                @click="dropdownOpen = !dropdownOpen"
            >

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