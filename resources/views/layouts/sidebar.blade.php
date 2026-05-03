<div class="flex flex-col h-full">

    {{-- Menu --}}
    <div class="flex flex-col flex-1 overflow-y-auto gap-5 no-scrollbar">

        {{-- Beranda --}}
        <div class="flex flex-col gap-2 w-full">
            <span :class="sidebarToggle ? 'text-left text-xs px-2' : 'text-center text-[1.4vh]'" class="block text-gray-600 font-semibold uppercase w-full">
                Beranda
            </span>

            {{-- Menu Beranda --}}
            <div class="flex flex-col space-y-2 w-full text-sm">
                <a href="{{ route('kepegawaian') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('kepegawaian') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                    <i class="text-lg fa-solid fa-address-card"></i>
                    <span :class="sidebarToggle ? 'block' : 'hidden'">Kepegawaian</span>
                </a>
                <a href="{{ route('presensi') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('presensi') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                    <i class="text-xl fa-solid fa-clock"></i>
                    <span :class="sidebarToggle ? 'block' : 'hidden'">Presensi</span>
                </a>

                @if (auth()->user()->hasRole([
                        'Admin',
                        'SDM Yayasan',
                        'SDM Universitas',
                        'Pimpinan',
                        'Staff',
                        'Tendik'
                ]))
                    <a href="{{ route('lembur') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('lembur') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                        <i class="text-lg fa-solid fa-business-time"></i>
                        <span :class="sidebarToggle ? 'block' : 'hidden'">Lembur</span>
                    </a>
                @endif
                <a href="{{ route('cuti') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('cuti') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                    <i class="text-lg fa-solid fa-plane-departure"></i>
                    <span :class="sidebarToggle ? 'block' : 'hidden'">Cuti</span>
                </a>
                <a href="{{ route('surat-menyurat') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('surat-menyurat') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                    <i class="text-xl fa-solid fa-envelope"></i>
                    <span :class="sidebarToggle ? 'block' : 'hidden'">Surat Menyurat</span>
                </a>
            </div>
        </div>

        {{-- Manajemen (Khusus Admin) --}}
        @if (auth()->user()->hasRole([
                'Admin',
                'SDM Yayasan',
                'SDM Universitas',
                'Pimpinan'
        ]))
            <div class="flex flex-col gap-2 w-full text-sm">
                <span :class="sidebarToggle ? 'text-left text-xs px-2' : 'text-center text-[1.4vh]'" class="block text-gray-600 font-semibold uppercase w-full">
                    Manajemen
                </span>

                {{-- Menu Manajemen --}}
                @if (auth()->user()->hasRole('Admin'))
                    <div class="flex flex-col space-y-2 w-full">
                        <a href="{{ route('manajemen-pengguna') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('manajemen-pengguna') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                            <i class="text-lg fa-solid fa-address-book"></i>
                            <span :class="sidebarToggle ? 'block' : 'hidden'">Pengguna</span>
                        </a>
                    </div>
                @endif
                <div class="flex flex-col space-y-2 w-full">
                    <a href="{{ route('manajemen-pegawai') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('manajemen-pegawai') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                        <i class="text-md fa-solid fa-users"></i>
                        <span :class="sidebarToggle ? 'block' : 'hidden'">Pegawai</span>
                    </a>
                </div>
                <div class="flex flex-col space-y-2 w-full">
                    <a href="{{ route('manajemen-presensi') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('manajemen-presensi') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                        <i class="text-lg fa-solid fa-list-check"></i>
                        <span :class="sidebarToggle ? 'block' : 'hidden'">Presensi</span>
                    </a>
                </div>
                <div class="flex flex-col space-y-2 w-full">
                    <a href="{{ route('manajemen-lembur') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('manajemen-lembur') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                        <i class="text-md fa-solid fa-user-check"></i>
                        <span :class="sidebarToggle ? 'block' : 'hidden'">Pengajuan Lembur</span>
                    </a>
                </div>
                <div class="flex flex-col space-y-2 w-full">
                    <a href="{{ route('manajemen-cuti') }}" :class="sidebarToggle ? 'justify-start gap-3' : 'justify-center'" class="flex items-center w-full p-2 rounded-md transition hover:bg-gradient-to-r hover:from-[#2B76FF]/50 hover:to-[#A8C7FF]/50 hover:text-white cursor-pointer text-gray-800 {{ request()->routeIs('manajemen-cuti') ? 'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white' : ''}}">
                        <i class="text-lg fa-solid fa-calendar-check"></i>
                        <span :class="sidebarToggle ? 'block' : 'hidden'">Pengajuan Cuti</span>
                    </a>
                </div>
            </div>
        @endif
    </div>


    {{-- Akun / Logout (Tetap di bawah) --}}
    <div class=" flex flex-col border-t border-gray-500 pt-3 mt-2 w-full gap-2">

        <div :class="sidebarToggle ? 'gap-2 justify-start' : 'justify-center'" class="flex items-center gap-2">
            <div :class="sidebarToggle ? 'rounded-xl' : 'rounded-md w-full'" class="w-9 h-9 bg-purple-200 text-gray-800 flex items-center justify-center">
                <i :class="sidebarToggle ? '' : 'text-lg'" class="fa-solid fa-user"></i>
            </div>

            <div class="flex flex-col" x-show="sidebarToggle" x-transition>
                <span class="text-sm font-semibold">{{ auth()->user()->username }}</span>
                <span class="text-xs text-gray-500">{{ auth()->user()->role->name }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('auth.handle.logout') }}">
            @csrf
            <button type="submit"
                    :class="sidebarToggle ? 'gap-2 justify-start' : 'justify-center'" 
                    class="w-full flex items-center gap-2 p-2 rounded-md text-red-600 bg-red-200/40 hover:bg-red-200/60 transition cursor-pointer">
                <i :class="sidebarToggle ? '' : 'text-lg'" class="fa-solid fa-right-from-bracket"></i>
                <span x-show="sidebarToggle" x-transition >Keluar</span>
            </button>
        </form>
    </div>

</div>