<div class="bg-white w-3xl h-[83vh] mx-auto rounded-2xl shadow-xl flex flex-col overflow-hidden">

    {{-- Header --}}
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl bg-indigo-500 flex items-center justify-center">
                <i class="fa-solid fa-file-import text-white text-lg"></i>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Detail Import Presensi
                </h2>

                <div class="flex items-center gap-0.5 mt-1 min-w-0">
                    <p class="text-xs text-gray-500 truncate self-center" title="{{ $file?->file_name }}">
                        {{ $file?->file_name }}
                    </p>
                </div>
            </div>
        </div>

        <button type="button" @click="$dispatch('close-detail-import')"
            class="w-9 h-9 rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </header>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/50">

        <div class="grid grid-cols-3 gap-4 mb-6 shrink-0 ">

            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 font-semibold mb-1">Total Baris Diproses</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($file?->total_rows ?? 0) }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-emerald-200 shadow-sm">
                <p class="text-xs text-emerald-600 font-semibold mb-1">Total Data Berhasil</p>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($file?->total_created ?? 0) }}</p>
            </div>

            {{-- <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs text-amber-500">Updated</p>
                <h3 class="text-2xl font-bold text-amber-600">
                    {{ number_format($file?->total_updated ?? 0) }}
                </h3>
            </div> --}}

            <div class="bg-white p-4 rounded-xl border border-red-200 shadow-sm">
                <p class="text-xs text-red-600 font-semibold mb-1">Total Data Gagal</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($file?->total_failed ?? 0) }}</p>
            </div>

        </div>

        <div class="rounded-2xl border border-gray-100 p-5 space-y-4">

            <h3 class="text-sm font-semibold text-gray-700">
                Informasi File
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>
                    <p class="text-gray-400">Periode</p>

                    <p class="font-medium text-gray-700">
                        {{ $file?->periode_mulai?->translatedFormat('d F Y') }}
                        -
                        {{ $file?->periode_selesai?->translatedFormat('d F Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400">Uploaded By</p>

                    <p class="font-medium text-gray-700">
                        {{ $file?->user?->pegawai?->nama ?? $file?->user?->username }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400">Tanggal Upload</p>

                    <p class="font-medium text-gray-700">
                        {{ $file?->created_at?->translatedFormat('d F Y | H:i') }}
                    </p>
                </div>

            </div>
        </div>

        @if (!empty($errorSummary))

            <div class="space-y-4">

                <div>
                    <h3 class="text-sm font-semibold text-gray-700">
                        Ringkasan Error
                    </h3>

                    <p class="text-xs text-gray-400">
                        Menampilkan daftar error saat proses import presensi.
                    </p>
                </div>

                @foreach ($errorSummary as $error)
                    <div x-data="{ open: false }" class="rounded-2xl border border-red-100 overflow-hidden">

                        {{-- Header --}}
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-4 bg-red-50 hover:bg-red-100 transition">

                            <div class="text-left">
                                <h4 class="text-sm font-semibold text-red-700">
                                    {{ $error['message'] }}
                                </h4>

                                <p class="text-xs text-red-500">
                                    {{ number_format($error['count']) }} data gagal
                                </p>
                            </div>

                            <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'">
                            </i>
                        </button>

                        {{-- Body --}}
                        <div x-show="open" x-collapse class="border-t border-red-100 bg-white p-4">

                            <div class="flex flex-wrap gap-2">

                                @foreach (array_slice($error['rows'], 0, 50) as $row)
                                    <span class="px-2 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs">
                                        Baris {{ $row }}
                                    </span>
                                @endforeach

                            </div>

                            @if (count($error['rows']) > 50)
                                <p class="mt-3 text-xs text-gray-400">
                                    dan {{ count($error['rows']) - 50 }} baris lainnya...
                                </p>
                            @endif

                        </div>

                    </div>
                @endforeach

            </div>

        @endif
    </div>

</div>
