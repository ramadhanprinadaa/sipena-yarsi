@props(['title' => 'Modul', 'icon' => 'fa-person-digging'])

<div class="flex flex-col items-center justify-center text-center min-h-[calc(100vh-460px)]">
    <div class="w-15 h-15 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center mb-4">
        <i class="fa-solid {{ $icon }} text-2xl"></i>
    </div>
    <h3 class="text-base font-semibold text-gray-800 mb-2">Data {{ $title }} Belum Tersedia</h3>
    <p class="text-xs text-gray-500 max-w-md mx-auto">
        Mohon Maaf, fitur untuk mengelola data {{ strtolower($title) }} saat ini sedang dalam tahap pengembangan. Silakan kembali lagi nanti.
    </p>
</div>