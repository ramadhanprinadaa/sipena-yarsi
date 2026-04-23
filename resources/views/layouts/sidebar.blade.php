<div class="flex flex-col h-full"
    x-data="{
        open: $persist({
            beranda: true,
            manajemen: true,
            konfigurasi: true
        }),

        floating: null,

        toggle(name){
            if(!sidebarToggle){
                this.floating = this.floating === name ? null : name
            } else {
                this.open[name] = !this.open[name]
            }
        },
    }"
>

    {{-- Menu --}}
    <div class="flex flex-col flex-1 overflow-y-auto gap-2 no-scrollbar">

        {{-- BERANDA --}}
        <div class="flex flex-col gap-1">

            {{-- Parent --}}
            <button
                @click="toggle('beranda')"
                :class="{
                    'justify-between': sidebarToggle,
                    'justify-center': !sidebarToggle,
                    'bg-pink-200':
                        {{ request()->routeIs('kepegawaian','presensi','lembur','cuti','surat-menyurat') ? 'true' : 'false' }}
                        && !(sidebarToggle && open.beranda)
                }"
                class="flex items-center w-full p-2 rounded-md hover:bg-pink-200 cursor-pointer"
                >

                <div :class="sidebarToggle ? 'gap-3' : 'flex-col gap-2'" class="flex items-center">
                    <i class="fa-solid fa-house text-lg"></i>
                    <span :class="sidebarToggle ? '' : 'text-xs'">Beranda</span>
                </div>

                <i
                    x-show="sidebarToggle"
                    :class="open.beranda ? 'rotate-90' : ''"
                    class="fa-solid fa-chevron-right text-xs transition">
                </i>
            </button>

            {{-- Sub Menu --}}
            <div x-show="sidebarToggle && open.beranda" x-transition class="ml-2 flex flex-col gap-2">

                <a wire:navigate href="{{ route('kepegawaian') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('kepegawaian') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-address-card"></i>
                    <span>Kepegawaian</span>
                </a>

                <a wire:navigate href="{{ route('presensi') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('presensi') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Presensi</span>
                </a>

                <a wire:navigate href="{{ route('lembur') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('lembur') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-business-time"></i>
                    <span>Lembur</span>
                </a>

                <a wire:navigate href="{{ route('cuti') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('cuti') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-plane-departure"></i>
                    <span>Cuti</span>
                </a>

                <a wire:navigate href="{{ route('surat-menyurat') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('surat-menyurat') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Surat Menyurat</span>
                </a>

            </div>

            {{-- Sub Menu Floating --}}
            <div
                x-show="!sidebarToggle && floating === 'beranda'"
                @mouseleave="floating = null"
                x-transition
                class="absolute left-24 top-0 w-56 bg-white shadow-lg rounded-lg p-2"
            >
                <a wire:navigate href="{{ route('kepegawaian') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('kepegawaian') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-address-card"></i>
                    <span>Kepegawaian</span>
                </a>
                <a wire:navigate href="{{ route('presensi') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('presensi') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Presensi</span>
                </a>

                <a wire:navigate href="{{ route('lembur') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('lembur') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-business-time"></i>
                    <span>Lembur</span>
                </a>

                <a wire:navigate href="{{ route('cuti') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('cuti') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-plane-departure"></i>
                    <span>Cuti</span>
                </a>

                <a wire:navigate href="{{ route('surat-menyurat') }}"
                    class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('surat-menyurat') ? 'bg-pink-200' : ''}}">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Surat Menyurat</span>
                </a>
            </div>
        </div>

        {{-- MANAJEMEN --}}
        @if (auth()->user()->hasRole([
                'Admin',
                'SDM Yayasan',
                'SDM Universitas',
                'Pimpinan'
        ]))

            <div class="flex flex-col gap-1">
                {{-- Parent --}}
                <button
                    @click="toggle('manajemen')"
                    :class="{
                        'justify-between': sidebarToggle,
                        'justify-center': !sidebarToggle,
                        'bg-pink-200':
                            {{ request()->routeIs('manajemen-pengguna','manajemen-pegawai','manajemen-presensi', 'manajemen-lembur','manajemen-cuti','manajemen-surat-menyurat') ? 'true' : 'false' }}
                            && !(sidebarToggle && open.manajemen)
                    }"
                    class="flex items-center w-full p-2 rounded-md hover:bg-pink-200 cursor-pointer"
                    >

                    <div :class="sidebarToggle ? 'gap-3' : 'flex-col gap-1'" class="flex items-center">
                        <i class="fa-solid fa-users-gear"></i>
                        <span :class="sidebarToggle ? '' : 'text-xs'">Manajemen</span>
                    </div>

                    <i
                        x-show="sidebarToggle"
                        :class="open.manajemen ? 'rotate-90' : ''"
                        class="fa-solid fa-chevron-right text-xs transition">
                    </i>
                </button>

                {{-- Sub Menu --}}
                <div x-show="open.manajemen && sidebarToggle" x-transition class="ml-2 flex flex-col gap-2">

                    @if (auth()->user()->hasRole('Admin'))
                        <a wire:navigate href="{{ route('manajemen-pengguna') }}"
                            class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-pengguna') ? 'bg-pink-200' : ''}}">
                            <i class="fa-solid fa-address-book text-lg"></i>
                            <span>Pengguna</span>
                        </a>
                    @endif

                    <a wire:navigate href="{{ route('manajemen-pegawai') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-pegawai') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-users"></i>
                        <span>Pegawai</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-presensi') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-presensi') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-list-check text-lg"></i>
                        <span>Presensi</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-lembur') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-lembur') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Pengajuan Lembur</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-cuti') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-cuti') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                </div>

                {{-- Sub Menu Floating --}}
                <div
                    x-show="floating === 'manajemen' && !sidebarToggle"
                    @mouseleave="floating = null"
                    x-transition
                    class="absolute left-24 top-12 w-56 bg-white shadow-lg rounded-lg p-2"
                >
                    @if (auth()->user()->hasRole('Admin'))
                        <a wire:navigate href="{{ route('manajemen-pengguna') }}"
                            class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-pengguna') ? 'bg-pink-200' : ''}}">
                            <i class="fa-solid fa-address-book text-lg"></i>
                            <span>Pengguna</span>
                        </a>
                    @endif

                    <a wire:navigate href="{{ route('manajemen-pegawai') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-pegawai') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-users"></i>
                        <span>Pegawai</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-presensi') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-presensi') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-list-check text-lg"></i>
                        <span>Presensi</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-lembur') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-lembur') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Pengajuan Lembur</span>
                    </a>

                    <a wire:navigate href="{{ route('manajemen-cuti') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('manajemen-cuti') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- KONFIGURASI --}}
        @if (auth()->user()->hasRole('Admin'))
            <div class="flex flex-col gap-1">

                {{-- Parent --}}
                <button
                    @click="toggle('konfigurasi')"
                    :class="{
                        'justify-between': sidebarToggle,
                        'justify-center': !sidebarToggle,
                        'bg-pink-200':
                            {{ request()->routeIs('konfigurasi-unit-kerja', 'konfigurasi-alur-persetujuan', 'konfigurasi-hari-libur') ? 'true' : 'false' }}
                            && !(sidebarToggle && open.konfigurasi)
                    }"
                    class="flex items-center w-full p-2 rounded-md hover:bg-pink-200 cursor-pointer"
                    >

                    <div :class="sidebarToggle ? 'gap-3' : 'flex-col gap-1'" class="flex items-center">
                        <i class="fa-solid fa-gear text-xl"></i>
                        <span :class="sidebarToggle ? '' : 'text-xs'">Konfigurasi</span>
                    </div>

                    <i
                        x-show="sidebarToggle"
                        :class="open.konfigurasi ? 'rotate-90' : ''"
                        class="fa-solid fa-chevron-right text-xs transition">
                    </i>
                </button>

                {{-- Sub Menu --}}
                <div x-show="open.konfigurasi && sidebarToggle" x-transition class="ml-2 flex flex-col gap-2">

                    <a wire:navigate href="{{ route('konfigurasi-unit-kerja') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('konfigurasi-unit-kerja') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-building text-lg"></i>
                        <span>Unit Kerja</span>
                    </a>

                    <a wire:navigate href="{{ route('konfigurasi-alur-persetujuan') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('konfigurasi-alur-persetujuan') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-diagram-project"></i>
                        <span>Alur Persetujuan</span>
                    </a>

                    <a wire:navigate href="{{ route('konfigurasi-hari-libur') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('konfigurasi-hari-libur') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-calendar-plus text-lg"></i>
                        <span>Hari Libur</span>
                    </a>
                </div>

                {{-- Sub Menu Floating --}}
                <div
                    x-show="floating === 'konfigurasi' && !sidebarToggle"
                    @mouseleave="floating = null"
                    x-transition
                    class="absolute left-24 top-26 w-56 bg-white shadow-lg rounded-lg p-2"
                >

                    <a wire:navigate href="{{ route('konfigurasi-unit-kerja') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('konfigurasi-unit-kerja') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-building text-lg"></i>
                        <span>Unit Kerja</span>
                    </a>

                    <a wire:navigate href="{{ route('konfigurasi-alur-persetujuan') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('konfigurasi-alur-persetujuan') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-diagram-project"></i>
                        <span>Alur Persetujuan</span>
                    </a>

                    <a wire:navigate href="{{ route('konfigurasi-hari-libur') }}"
                        class="flex items-center gap-3 p-2 rounded-md hover:bg-pink-200 text-sm {{ request()->routeIs('konfigurasi-hari-libur') ? 'bg-pink-200' : ''}}">
                        <i class="fa-solid fa-calendar-plus text-lg"></i>
                        <span>Hari Libur</span>
                    </a>
                </div>
            </div>
        @endif
    </div>


    {{-- Akun / Logout (Tetap di bawah) --}}
    <div class=" flex flex-col border-t border-gray-300 pt-3 mt-2 w-full gap-2">

        <div :class="sidebarToggle ? 'gap-2 justify-start' : 'justify-center'" class="flex items-center gap-2">
            <div :class="sidebarToggle ? 'rounded-md' : 'rounded-md w-full'" class="w-10 h-10 bg-pink-200 text-gray-800 flex items-center justify-center">
                <i :class="sidebarToggle ? 'text-lg' : 'text-lg'" class="fa-solid fa-user"></i>
            </div>

            <div class="flex flex-col" x-show="sidebarToggle" x-transition>
                <span class="text-sm font-semibold">{{ auth()->user()->username }}</span>
                <span class="text-xs text-gray-500">{{ auth()->user()->role->name }}</span>
            </div>
        </div>
    </div>

</div>