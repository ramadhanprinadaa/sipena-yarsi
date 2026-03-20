<div class="flex flex-col h-screen">

    {{-- Logo --}}
    <div class="flex items-center justify-center bg-white gap-2 p-2 h-16 w-full border-b border-gray-300 cursor-pointer">
        <img src="{{ asset('favicon.ico') }}" alt="Logo" class="w-10 h-10 object-contain">

        <div class="flex flex-col leading-tight menu-text">
            <span class="text-gray-600 font-bold">SIPENA</span>
            <span class="text-gray-500 text-xs">
                Sistem Informasi Pegawai dan Administrasi YARSI
            </span>
        </div>
    </div>

    {{-- Container --}}
    <div class="flex flex-col flex-1 bg-white p-2">

        {{-- Menu --}}
        <div class="space-y-2">
            <span class="text-gray-600 text-sm px-2 menu-text font-bold uppercase">Menu Utama</span>

            
            <a href="{{ route('kepegawaian') }}"
               class="flex items-center gap-2 p-2 hover:bg-cyan-100 rounded-md
               {{ request()->routeIs('kepegawaian') ? 'bg-cyan-100' : '' }}"
            >
                <i class="sidebar-icon fa-solid fa-address-card w-7" data-tippy-content="Kepegawaian"></i>
                <span class="menu-text">Kepegawaian</span>
            </a>

            <a href="{{ route('presensi') }}" 
               class="flex items-center gap-2 p-2 hover:bg-cyan-100 rounded-md
               {{ request()->routeIs('presensi') ? 'bg-cyan-100' : '' }}"
            >
                <i class="sidebar-icon fa-solid fa-user-check w-7"></i>
                <span class="menu-text">Presensi</span>
            </a>

            <a href="{{ route('lembur') }}" 
               class="flex items-center gap-2 p-2 hover:bg-cyan-100 rounded-md
               {{ request()->routeIs('lembur') ? 'bg-cyan-100' : '' }}"
            >
                <i class="sidebar-icon fa-solid fa-business-time w-7"></i>
                <span class="menu-text">Lembur</span>
            </a>

            <a href="{{ route('cuti') }}" 
               class="flex items-center gap-2 p-2 hover:bg-cyan-100 rounded-md
               {{ request()->routeIs('cuti') ? 'bg-cyan-100' : '' }}"
            >
                <i class="sidebar-icon fa-solid fa-plane-departure w-7"></i>
                <span class="menu-text">Cuti</span>
            </a>
        </div>

        {{-- Akun Saya --}}
        <div class="mt-auto mb-1">
            <span class="text-gray-600 text-sm px-2 menu-text uppercase font-bold">Akun Saya</span>

            <div class="flex items-center gap-3 p-2 mt-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                <i class="fa-solid fa-user text-xl text-gray-600 w-7 text-center"></i>
                <div class="flex flex-col leading-tight menu-text">
                    <span class="text-sm font-semibold text-gray-700">Hilal Rizqi Akbar</span>
                    <span class="text-xs text-gray-500">Pegawai Tendik</span>
                </div>
            </div>

            <a href="/login"
               class="flex items-center gap-2 p-2 mt-2 bg-red-100 hover:bg-red-200 rounded-md cursor-pointer">
                <i class="sidebar-icon fa-solid fa-right-from-bracket w-7 text-red-500"></i>
                <span class="menu-text text-red-500">Keluar</span>
            </a>
        </div>
    </div>
</div>