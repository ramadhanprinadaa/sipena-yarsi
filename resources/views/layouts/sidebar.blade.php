<div class="flex flex-col h-full" x-data="{
    open: $persist({
        beranda: true,
        manajemen: true,
        konfigurasi: true,
        sumber_daya: true,
    }),

    floating: null,

    toggle(name) {
        if (!sidebarToggle) {
            this.floating = this.floating === name ? null : name
        } else {
            this.open[name] = !this.open[name]
        }
    },
}">

    {{-- Menu --}}
    <div class="flex flex-col flex-1 overflow-y-auto gap-2 no-scrollbar">

        {{-- BERANDA --}}
        <div class="flex flex-col gap-1">

            {{-- Parent --}}
            <button @click="toggle('beranda')"
                :class="{
                    'justify-between': sidebarToggle,
                    'justify-center': !sidebarToggle,
                    'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white': {{ request()->routeIs('kepegawaian', 'presensi', 'lembur', 'cuti', 'surat-menyurat') ? 'true' : 'false' }} &&
                        !(sidebarToggle && open.beranda)
                }"
                class="flex items-center w-full p-2 rounded-md hover:bg-gradient-to-r hover:from-[#2B76FF] hover:to-[#A8C7FF] hover:text-white cursor-pointer">

                <div :class="sidebarToggle ? 'gap-3' : 'flex-col gap-2'" class="flex items-center">
                    <i class="fa-solid fa-house text-lg"></i>
                    <span :class="sidebarToggle ? '' : 'text-xs'">Beranda</span>
                </div>

                <i x-cloak x-show="sidebarToggle" :class="open.beranda ? 'rotate-90' : ''"
                    class="fa-solid fa-chevron-right text-xs transition">
                </i>
            </button>

            {{-- Sub Menu --}}
            <div x-cloak x-show="sidebarToggle && open.beranda" x-transition class="ml-2 flex flex-col gap-2">

                <a wire:navigate href="{{ route('kepegawaian') }}"
                    class="nav-link {{ request()->routeIs('kepegawaian') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-address-card"></i>
                    <span>Kepegawaian</span>
                </a>

                <a wire:navigate href="{{ route('presensi') }}"
                    class="nav-link {{ request()->routeIs('presensi') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Presensi</span>
                </a>

                @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas', 'Rektor', 'Pimpinan', 'Staff']) &&
                        auth()->user()->pegawai?->jenis_pegawai?->jenis !== 'Tenaga Pendidik')
                    <a wire:navigate href="{{ route('lembur') }}"
                        class="nav-link {{ request()->routeIs('lembur') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-business-time"></i>
                        <span>Lembur</span>
                    </a>
                @endif

                <a wire:navigate href="{{ route('cuti') }}"
                    class="nav-link {{ request()->routeIs('cuti') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-plane-departure"></i>
                    <span>Cuti</span>
                </a>

                <a wire:navigate href="{{ route('surat-menyurat') }}"
                    class="nav-link {{ request()->routeIs('surat-menyurat') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Surat Menyurat</span>
                </a>

            </div>

            {{-- Sub Menu Floating --}}
            <div x-cloak x-show="!sidebarToggle && floating === 'beranda'" @mouseleave="floating = null" x-transition
                class="absolute left-26 top-0 w-56 bg-white shadow-lg rounded-lg p-2 flex flex-col gap-1">
                <a wire:navigate href="{{ route('kepegawaian') }}"
                    class="nav-link {{ request()->routeIs('kepegawaian') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-address-card"></i>
                    <span>Kepegawaian</span>
                </a>
                <a wire:navigate href="{{ route('presensi') }}"
                    class="nav-link {{ request()->routeIs('presensi') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Presensi</span>
                </a>
                @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas', 'Rektor', 'Pimpinan', 'Staff']) &&
                        auth()->user()->pegawai?->jenis_pegawai?->jenis !== 'Tenaga Pendidik')
                    <a wire:navigate href="{{ route('lembur') }}"
                        class="nav-link {{ request()->routeIs('lembur') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-business-time"></i>
                        <span>Lembur</span>
                    </a>
                @endif
                <a wire:navigate href="{{ route('cuti') }}"
                    class="nav-link {{ request()->routeIs('cuti') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-plane-departure"></i>
                    <span>Cuti</span>
                </a>
                <a wire:navigate href="{{ route('surat-menyurat') }}"
                    class="nav-link {{ request()->routeIs('surat-menyurat') ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Surat Menyurat</span>
                </a>
            </div>
        </div>

        {{-- MANAJEMEN --}}
        @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas', 'Rektor', 'Pimpinan']))

            <div class="flex flex-col gap-1">
                {{-- Parent --}}
                <button @click="toggle('manajemen')"
                    :class="{
                        'justify-between': sidebarToggle,
                        'justify-center': !sidebarToggle,
                        'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white': {{ request()->routeIs(
                            'manajemen-pengguna',
                            'manajemen-pegawai',
                            'manajemen-presensi',
                            'manajemen-lembur',
                            'manajemen-cuti',
                            'manajemen-surat-menyurat',
                            'manajemen-pegawai-detail',
                        )
                            ? 'true'
                            : 'false' }} &&
                            !(sidebarToggle && open.manajemen)
                    }"
                    class="flex items-center w-full p-2 rounded-md hover:bg-gradient-to-r hover:from-[#2B76FF] hover:to-[#A8C7FF] hover:text-white cursor-pointer">

                    <div :class="sidebarToggle ? 'gap-3' : 'flex-col gap-1'" class="flex items-center">
                        <i class="fa-solid fa-users-gear"></i>
                        <span :class="sidebarToggle ? '' : 'text-xs'">Manajemen</span>
                    </div>

                    <i x-cloak x-show="sidebarToggle" :class="open.manajemen ? 'rotate-90' : ''"
                        class="fa-solid fa-chevron-right text-xs transition">
                    </i>
                </button>

                {{-- Sub Menu --}}
                <div x-cloak x-show="open.manajemen && sidebarToggle" x-transition class="ml-2 flex flex-col gap-2">

                    @if (auth()->user()->hasRole('Admin'))
                        <a wire:navigate href="{{ route('manajemen-pengguna') }}"
                            class="nav-link {{ request()->routeIs('manajemen-pengguna') ? 'nav-link-active' : 'nav-link-inactive' }}">
                            <i class="fa-solid fa-address-book text-lg"></i>
                            <span>Pengguna</span>
                        </a>
                    @endif

                    <a wire:navigate href="{{ route('manajemen-pegawai') }}"
                        class="nav-link {{ request()->routeIs('manajemen-pegawai', 'manajemen-pegawai-detail') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Pegawai</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-presensi') }}"
                        class="nav-link {{ request()->routeIs('manajemen-presensi') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-list-check text-lg"></i>
                        <span>Presensi</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-lembur') }}"
                        class="nav-link {{ request()->routeIs('manajemen-lembur') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Pengajuan Lembur</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-cuti') }}"
                        class="nav-link {{ request()->routeIs('manajemen-cuti') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                </div>

                {{-- Sub Menu Floating --}}
                <div x-cloak x-show="floating === 'manajemen' && !sidebarToggle" @mouseleave="floating = null" x-transition
                    class="absolute left-26 top-18 w-56 bg-white shadow-lg rounded-lg p-2 flex flex-col gap-1">
                    @if (auth()->user()->hasRole('Admin'))
                        <a wire:navigate href="{{ route('manajemen-pengguna') }}"
                            class="nav-link {{ request()->routeIs('manajemen-pengguna') ? 'nav-link-active' : 'nav-link-inactive' }}">
                            <i class="fa-solid fa-address-book text-lg"></i>
                            <span>Pengguna</span>
                        </a>
                    @endif

                    <a wire:navigate href="{{ route('manajemen-pegawai') }}"
                        class="nav-link {{ request()->routeIs('manajemen-pegawai', 'manajemen-pegawai-detail') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Pegawai</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-presensi') }}"
                        class="nav-link {{ request()->routeIs('manajemen-presensi') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-list-check text-lg"></i>
                        <span>Presensi</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-lembur') }}"
                        class="nav-link {{ request()->routeIs('manajemen-lembur') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Pengajuan Lembur</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-cuti') }}"
                        class="nav-link {{ request()->routeIs('manajemen-cuti') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- KONFIGURASI --}}
        @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
            <div class="flex flex-col gap-1">

                {{-- Parent --}}
                <button @click="toggle('konfigurasi')"
                    :class="{
                        'justify-between': sidebarToggle,
                        'justify-center': !sidebarToggle,
                        'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white': {{ request()->routeIs('konfigurasi-unit-kerja', 'konfigurasi-alur-persetujuan', 'konfigurasi-kalender') ? 'true' : 'false' }} &&
                            !(sidebarToggle && open.konfigurasi)
                    }"
                    class="flex items-center w-full p-2 rounded-md hover:bg-gradient-to-r hover:from-[#2B76FF] hover:to-[#A8C7FF] hover:text-white cursor-pointer">

                    <div :class="sidebarToggle ? 'gap-3' : 'flex-col gap-1'" class="flex items-center">
                        <i class="fa-solid fa-gear text-xl"></i>
                        <span :class="sidebarToggle ? '' : 'text-xs'">Konfigurasi</span>
                    </div>

                    <i x-cloak x-show="sidebarToggle" :class="open.konfigurasi ? 'rotate-90' : ''"
                        class="fa-solid fa-chevron-right text-xs transition">
                    </i>
                </button>

                {{-- Sub Menu --}}
                <div x-cloak x-show="open.konfigurasi && sidebarToggle" x-transition class="ml-2 flex flex-col gap-2">

                    <a wire:navigate href="{{ route('konfigurasi-unit-kerja') }}"
                        class="nav-link {{ request()->routeIs('konfigurasi-unit-kerja') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-building text-lg"></i>
                        <span>Unit Kerja</span>
                    </a>
                    <a wire:navigate href="{{ route('konfigurasi-kalender') }}"
                        class="nav-link {{ request()->routeIs('konfigurasi-kalender') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-calendar-plus text-lg"></i>
                        <span>Kalender</span>
                    </a>
                </div>

                {{-- Sub Menu Floating --}}
                <div x-cloak x-show="floating === 'konfigurasi' && !sidebarToggle" @mouseleave="floating = null" x-transition
                    class="absolute left-26 top-32 w-56 bg-white shadow-lg rounded-lg p-2 flex flex-col gap-1">

                    <a wire:navigate href="{{ route('konfigurasi-unit-kerja') }}"
                        class="nav-link {{ request()->routeIs('konfigurasi-unit-kerja') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-building text-lg"></i>
                        <span>Unit Kerja</span>
                    </a>
                    <a wire:navigate href="{{ route('konfigurasi-kalender') }}"
                        class="nav-link {{ request()->routeIs('konfigurasi-kalender') ? 'nav-link-active' : 'nav-link-inactive' }}">
                        <i class="fa-solid fa-calendar-plus text-lg"></i>
                        <span>Kalender</span>
                    </a>
                </div>
            </div>
        @endif


        {{-- SUMBER DAYA --}}
        <a wire:navigate href="{{ route('sumber-daya') }}"
            :class="{
                'justify-start gap-3': sidebarToggle,
                'justify-center flex-col gap-1': !sidebarToggle,
                'bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white': {{ request()->routeIs('sumber-daya') ? 'true' : 'false' }}
            }"
            class="flex items-center w-full p-2 rounded-md hover:bg-gradient-to-r hover:from-[#2B76FF] hover:to-[#A8C7FF] hover:text-white transition-all duration-200">
            <i class="fa-solid fa-book-open text-lg"></i>
            <span :class="sidebarToggle ? '' : 'text-xs text-center'">
                Sumber Daya
            </span>
        </a>
    </div>


    {{-- Akun --}}
    <div class=" flex flex-col border-t border-gray-300 pt-3 mt-2 w-full gap-2">

        <div :class="sidebarToggle ? 'gap-2 justify-start' : 'justify-center'" class="flex items-center gap-2">
            <div :class="sidebarToggle ? 'rounded-md' : 'rounded-md w-full'"
                class="w-10 h-10 bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] text-white text-gray-800 flex items-center justify-center">
                <i :class="sidebarToggle ? 'text-lg' : 'text-lg'" class="fa-solid fa-user"></i>
            </div>

            <div class="flex flex-col overflow-hidden" x-cloak x-show="sidebarToggle" x-transition>
                <span
                    class="text-sm font-semibold truncate">{{ auth()->user()->pegawai?->nama ?? auth()->user()->username }}</span>
                <span
                    class="text-xs text-gray-500">{{ auth()->user()->role->name }}</span>
            </div>
        </div>
    </div>

</div>
