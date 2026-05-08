<div class="bg-white w-[45vw] max-w-[95vw] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">

    @if(!$showResult)
        <!-- Upload Mode -->

        <!-- Header -->
        <header class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-indigo-100">
                    <i class="fa-solid fa-cloud-arrow-down text-base text-indigo-500"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800 leading-tight tracking-tight">Import Data Pegawai</h2>
                    <p class="text-xs text-gray-400 leading-tight">Unggah file Excel atau CSV untuk import massal data pegawai</p>
                </div>
            </div>
            <button type="button"
                    @click="$dispatch('close-import-modal')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <!-- Body -->
        <div class="px-6 py-5 flex flex-col gap-5">

            <!-- Download Template -->
            <div class="flex items-center justify-between px-4 py-3 rounded-xl border border-indigo-100 bg-gradient-to-r from-[#f5f3ff] to-[#eef2ff]"
                >
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center border border-indigo-100">
                        <i class="fa-solid fa-file-lines text-md text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-700">Template Import Pegawai</p>
                        <p class="text-[11px] text-gray-400">Gunakan template ini agar format file sesuai sistem</p>
                    </div>
                </div>
                <button
                    wire:click="downloadTemplate"
                    type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-md shadow-sm transition-all cursor-pointer hover:opacity-90 active:scale-95 bg-gradient-to-br from-[#6366f1] to-[#818cf8]">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Template

                    <!-- Loading -->
                    <svg aria-hidden="true" wire:loading wire:target="downloadTemplate"
                        class="w-4 h-4 text-neutral-quaternary animate-spin fill-brand" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </button>
            </div>

            <!-- Dropzone -->
            <div>
                <label class="block text-xs font-semibold text-gray-600">
                    Upload File Excel / CSV
                    <span class="text-red-400 ml-0.5">*</span>
                </label>

                <div class="relative flex items-center justify-center w-full mt-2">

                    <!-- Loading -->
                    <div wire:loading wire:target="file">
                        <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                            <div class="flex flex-col items-center gap-2">
                                <x-ui.spinner />
                                <p class="text-xs text-gray-500 font-medium">
                                    Mengunggah file...
                                </p>
                            </div>
                        </div>
                    </div>

                    <label
                        @php
                            $dropzoneClass = 'border-gray-300 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-300';
                            if (!empty($file)) {
                                $allowedExt = ['xls', 'xlsx', 'csv', 'ods', 'tsv'];
                                $ext = strtolower($file->getClientOriginalExtension());
                                if (in_array($ext, $allowedExt)) {
                                    $dropzoneClass = 'border-emerald-300 bg-emerald-50/70';
                                } else {
                                    $dropzoneClass = 'border-red-300 bg-red-50/70';
                                }
                            }
                        @endphp
                        for="dropzone-file"
                        class="group relative flex flex-col items-center justify-center w-full min-h-[220px] rounded-2xl border-2 border-dashed transition-all duration-200 cursor-pointer overflow-hidden {{ $dropzoneClass }}">

                        <input
                            wire:model="file"
                            id="dropzone-file"
                            type="file"
                            accept=".xls,.xlsx,.csv,.ods,.tsv"
                            class="hidden"
                        />

                        @if(!$file)
                            <!-- Empty State -->
                            <div class="flex flex-col items-center justify-center px-6 py-8 text-center">
                                <div class="w-14 h-14 rounded-full bg-white shadow-sm border border-gray-200 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                    <svg class="w-7 h-7 text-indigo-500"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 17h3a3 3 0 000-6h-.025A5.5 5.5 0 007.207 9.021A4 4 0 007 17h2m3 4V10m0 0l-3 3m3-3l3 3"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">
                                    Klik untuk upload file
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    atau drag & drop file ke area ini
                                </p>
                                <div class="flex flex-wrap justify-center gap-2 mt-5">
                                    @foreach(['XLSX', 'XLS', 'CSV', 'ODS', 'TSV'] as $ext)
                                        <span class="px-2 py-1 rounded-md text-[10px] font-semibold bg-white border border-gray-200 text-gray-500">
                                            {{ $ext }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="text-[11px] text-gray-400 mt-4">
                                    Maksimal ukuran file 10 MB
                                </p>
                            </div>
                        @elseif($file)
                            <!-- File Selected -->
                            <div class="w-full h-full flex flex-col items-center justify-center px-6 py-6 text-center">
                                @php
                                    $extension = strtolower($file->getClientOriginalExtension());
                                    if (in_array($extension, ['xls', 'xlsx', 'ods'])) {
                                        $iconClass = 'fa-file-excel';
                                        $textColor = 'text-emerald-500';
                                        $borderColor = 'border-emerald-200';
                                    } elseif (in_array($extension, ['csv', 'tsv'])) {
                                        $iconClass = 'fa-file-csv';
                                        $textColor = 'text-teal-600';
                                        $borderColor = 'border-teal-300';
                                    } else {
                                        $iconClass = 'fa-file-lines';
                                        $textColor = 'text-gray-500';
                                        $borderColor = 'border-gray-200';
                                    }
                                @endphp
                                <div class="w-14 h-14 rounded-2xl bg-white shadow-sm border {{ $borderColor }} flex items-center justify-center mb-2">

                                    <i class="fa-solid {{ $iconClass }} text-2xl {{ $textColor }}"></i>
                                </div>
                                <div class="max-w-full">
                                    <p class="text-sm font-semibold text-gray-700 break-all">
                                        {{ $file->getClientOriginalName() }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ number_format($file->getSize() / 1024, 1) }} KB
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center gap-2 text-xs {{ $textColor }} font-medium">
                                    <i class="fa-solid fa-circle-check"></i>
                                    File berhasil dipilih
                                </div>
                                <p class="text-[11px] text-gray-400 mt-2">
                                    Klik area ini untuk mengganti file
                                </p>
                            </div>
                        @endif
                    </label>
                </div>
            </div>

            @error('file')
                <span class="mt-1 text-xs text-red-500">
                    {{ $message }}
                </span>
            @enderror

            <!-- Format Info -->
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach([
                    ['label' => '.xlsx', 'color' => 'bg-green-100 text-green-700'],
                    ['label' => '.xls',  'color' => 'bg-green-100 text-green-700'],
                    ['label' => '.csv',  'color' => 'bg-blue-100 text-blue-700'],
                    ['label' => '.ods',  'color' => 'bg-orange-100 text-orange-700'],
                    ['label' => '.tsv',  'color' => 'bg-purple-100 text-purple-700'],
                ] as $fmt)
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $fmt['color'] }}">
                        {{ $fmt['label'] }}
                    </span>
                @endforeach
                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-gray-500 ml-auto">
                    Maks. 10 MB
                </span>
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/60">

            <!-- Cancel Button -->
            <button
                type="button"
                @click="$dispatch('close-import-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition-all cursor-pointer">
                Batal
            </button>

            <!-- Save Button -->
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
                @disabled(!$file || $errors->any())
                type="button"
                class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white rounded-md shadow-sm transition-all cursor-pointer active:scale-95 {{ !$file || $errors->any() ? 'opacity-50 disabled:cursor-not-allowed' : 'hover:opacity-90' }}"
                style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);"
            >
                <!-- Loading spinner -->
                <svg wire:loading wire:target="save"
                    class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>

                <!-- Save icon -->
                <svg wire:loading.remove wire:target="save"
                    xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>

                <span wire:loading.remove wire:target="save">Upload File</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </footer>
    @else
        <!-- Import Result Preview -->

        <!-- Header -->
        <header class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-emerald-100">
                    <i class="fa-solid fa-circle-check text-base text-emerald-500"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800 leading-tight tracking-tight">
                        Hasil Import Data Pegawai
                    </h2>
                    <p class="text-xs text-gray-400 leading-tight">
                        Ringkasan dan detail hasil upload file pegawai
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="$dispatch('close-import-modal')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <!-- Body -->
        <div class="px-6 py-5 h-[70vh] flex flex-col">

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 shrink-0">

                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs text-gray-400 font-medium">Total Data</p>
                    <h3 class="mt-1 text-2xl font-bold text-gray-800">
                        {{ $importSummary['total_rows'] ?? 0 }}
                    </h3>
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-xs text-emerald-500 font-medium">Berhasil</p>
                    <h3 class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ $importSummary['success_rows'] ?? 0 }}
                    </h3>
                </div>

                <div class="rounded-2xl border border-red-100 bg-red-50 p-4">
                    <p class="text-xs text-red-500 font-medium">Gagal</p>
                    <h3 class="mt-1 text-2xl font-bold text-red-600">
                        {{ $importSummary['failed_rows'] ?? 0 }}
                    </h3>
                </div>

                <div class="rounded-2xl border border-yellow-100 bg-yellow-50 p-4">
                    <p class="text-xs text-yellow-500 font-medium">Duplikat</p>
                    <h3 class="mt-1 text-2xl font-bold text-yellow-600">
                        {{ $importSummary['duplicate_rows'] ?? 0 }}
                    </h3>
                </div>
            </div>

            <!-- Detail Rows -->
            <div x-data="{ tab: 'failed' }" class="mt-5 flex flex-col flex-1 min-h-0">

                <!-- Tab Button -->
                <div class="flex items-center gap-2 mb-3 shrink-0">
                    <button
                        @click="tab = 'failed'"
                        :class="tab === 'failed'
                            ? 'bg-red-100 text-red-700'
                            : 'bg-gray-100 text-gray-500'"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                        Failed
                        ({{ count($importSummary['failures'] ?? []) }})
                    </button>
                    <button
                        @click="tab = 'duplicate'"
                        :class="tab === 'duplicate'
                            ? 'bg-yellow-100 text-yellow-700'
                            : 'bg-gray-100 text-gray-500'"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                        Duplicate
                        ({{ count($importSummary['duplicates'] ?? []) }})
                    </button>

                    <button
                        @click="tab = 'success'"
                        :class="tab === 'success'
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-gray-100 text-gray-500'"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                        Success
                        ({{ count($importSummary['success_data'] ?? []) }})
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto pr-1 min-h-0">
                    <!-- Success Rows -->
                    @if(!empty($importSummary['success_data']))
                        <div class="space-y-3" x-show="tab === 'success'">

                            <div class="space-y-2">

                                @foreach($importSummary['success_data'] as $item)
                                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3">

                                        <div class="flex items-start justify-between gap-3">

                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">
                                                    Baris {{ $item['row'] }}
                                                </p>

                                                <p class="text-sm text-emerald-700 mt-1">
                                                    {{ $item['nama'] ?? '-' }}
                                                </p>

                                                <div class="flex flex-wrap gap-2 mt-2">

                                                    <span class="px-2 py-1 rounded-md text-[11px] font-medium bg-white border border-emerald-200 text-emerald-700">
                                                        NIP: {{ $item['nip'] ?? '-' }}
                                                    </span>

                                                    <span class="px-2 py-1 rounded-md text-[11px] font-medium bg-white border border-emerald-200 text-emerald-700">
                                                        {{ $item['unit_kerja'] ?? '-' }}
                                                    </span>

                                                </div>
                                            </div>

                                            <span class="px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 text-[11px] font-semibold">
                                                Success
                                            </span>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endif

                    <!-- Failed Rows -->
                    @if(!empty($importSummary['failures']))
                        <div class="space-y-3" x-show="tab === 'failed'">

                            <div class="space-y-2">

                                @foreach($importSummary['failures'] as $failure)

                                    <div class="rounded-xl border border-red-100 bg-red-50/70 px-4 py-3">

                                        <div class="flex items-start justify-between gap-3">

                                            <div class="flex-1">

                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        Baris {{ $failure['row'] }}
                                                    </p>

                                                    <span class="px-2 py-0.5 rounded-md bg-red-100 text-red-600 text-[10px] font-semibold uppercase">
                                                        {{ $failure['type'] }}
                                                    </span>
                                                </div>

                                                <p class="text-sm text-gray-700 mt-1">
                                                    {{ $failure['data']['nama'] ?? '-' }}
                                                </p>

                                                <ul class="mt-3 space-y-1">

                                                    @foreach($failure['errors'] as $error)
                                                        <li class="text-xs text-red-600 flex items-start gap-2">
                                                            <i class="fa-solid fa-circle text-[6px] mt-1.5"></i>
                                                            <span>{{ $error }}</span>
                                                        </li>
                                                    @endforeach

                                                </ul>

                                            </div>

                                        </div>
                                    </div>

                                @endforeach

                            </div>
                        </div>
                    @endif

                    <!-- Duplicate Rows -->
                    @if(!empty($importSummary['duplicates']))
                        <div class="space-y-3" x-show="tab === 'duplicate'">

                            <div class="space-y-2">

                                @foreach($importSummary['duplicates'] as $duplicate)

                                    <div class="rounded-xl border border-yellow-100 bg-yellow-50/70 px-4 py-3">

                                        <div class="flex items-start justify-between gap-3">

                                            <div>

                                                <p class="text-sm font-semibold text-gray-800">
                                                    Baris {{ $duplicate['row'] }}
                                                </p>

                                                <p class="text-sm text-gray-700 mt-1">
                                                    {{ $duplicate['data']['nama'] ?? '-' }}
                                                </p>

                                                <p class="text-xs text-yellow-700 mt-2">
                                                    {{ $duplicate['message'] }}
                                                </p>

                                            </div>

                                            <span class="px-2 py-1 rounded-md bg-yellow-100 text-yellow-700 text-[11px] font-semibold">
                                                Duplicate
                                            </span>

                                        </div>
                                    </div>

                                @endforeach

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/60">

            <button
                wire:click="resetImport"
                type="button"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition-all cursor-pointer">
                Import Lagi
            </button>

            <button
                type="button"
                @click="$dispatch('close-import-modal')"
                class="px-4 py-2 text-sm font-semibold text-white rounded-md transition-all cursor-pointer hover:opacity-90"
                style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);">
                Selesai
            </button>

        </footer>
    @endif
</div>
