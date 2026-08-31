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

        {{-- Notification --}}
        @php
            $headerNotifications = auth()->user()->notifications()->latest()->limit(50)->get();
            $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
            $notificationTabs = [
                'all' => 'Semua',
                'spl_diterbitkan' => 'SPL Baru',
                'spl_diperbarui' => 'SPL Diupdate',
                'laporan_lembur' => 'Laporan',
                'cuti' => 'Cuti',
            ];
        @endphp
        <div class="relative" x-data="{ notificationOpen: {{ $unreadNotificationCount > 0 ? 'true' : 'false' }}, activeTab: 'all' }">
            <button
                @click="notificationOpen = true"
                class="relative w-10 h-10 flex items-center justify-center rounded-full cursor-pointer bg-gradient-to-br from-blue-300 to-pink-200 text-gray-700 shadow-sm hover:shadow-md transition"
                aria-label="Buka notifikasi">
                <i class="fa-solid fa-bell text-lg"></i>
                @if($unreadNotificationCount > 0)
                    <span class="absolute -top-1 -right-1 min-w-4 h-4 px-1 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full">
                        {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                    </span>
                @endif
            </button>

            <template x-teleport="body">
            <div x-show="notificationOpen" x-cloak x-transition @click.outside="notificationOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
                <div @click.stop class="w-full max-w-2xl max-h-[80vh] overflow-hidden bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Notifikasi</h2>
                            <p class="text-xs text-gray-500">Informasi terbaru sesuai alur kerja Anda</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-800 cursor-pointer">Tandai semua dibaca</button>
                            </form>
                            <button @click="notificationOpen = false" class="w-8 h-8 text-gray-400 hover:text-gray-700 cursor-pointer" aria-label="Tutup notifikasi">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-1 overflow-x-auto px-4 py-3 border-b border-gray-100">
                        @foreach($notificationTabs as $tabKey => $tabLabel)
                            <button @click="activeTab = '{{ $tabKey }}'" :class="activeTab === '{{ $tabKey }}' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-50'" class="shrink-0 px-3 py-2 rounded-lg text-xs font-semibold transition cursor-pointer">
                                {{ $tabLabel }}
                            </button>
                        @endforeach
                    </div>

                    <div class="overflow-y-auto p-4 space-y-2">
                        @forelse($headerNotifications as $notification)
                            @php($category = $notification->data['category'] ?? 'all')
                            <a href="{{ route('notifications.read', $notification->id) }}" x-show="activeTab === 'all' || activeTab === '{{ $category }}'" class="flex gap-3 p-3 rounded-xl border {{ $notification->read_at ? 'border-gray-100 bg-white' : 'border-blue-100 bg-blue-50/60' }} hover:bg-gray-50 transition">
                                <div class="mt-0.5 w-8 h-8 rounded-lg flex items-center justify-center {{ str_starts_with($category, 'spl') ? 'bg-indigo-100 text-indigo-600' : ($category === 'cuti' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600') }}">
                                    <i class="fa-solid {{ str_starts_with($category, 'spl') ? 'fa-file-lines' : ($category === 'cuti' ? 'fa-calendar-check' : 'fa-clipboard-check') }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="text-sm font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notifikasi SIPENA' }}</p>
                                        @unless($notification->read_at)<span class="mt-1 w-2 h-2 shrink-0 rounded-full bg-blue-600"></span>@endunless
                                    </div>
                                    <p class="mt-1 text-xs text-gray-600">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="mt-2 text-[11px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="py-12 text-center text-gray-500">
                                <i class="fa-regular fa-bell-slash text-2xl mb-2"></i>
                                <p class="text-sm">Belum ada notifikasi.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            </template>
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
            <div x-cloak x-show="dropdownOpen" x-transition
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
