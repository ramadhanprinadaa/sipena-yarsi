<div class="relative flex flex-col gap-4 p-5 sm:p-3 items-center h-full lg:h-[calc(100vh-180px)]">

    {{-- Badge --}}
    <div class="absolute left-1/2 -translate-x-1/2 -top-7 z-20">
        <div
            class="px-3 py-2 rounded-md bg-blue-500 text-white text-[11px] sm:text-xs font-semibold shadow-md border border-white/20">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 17h3a3 3 0 000-6h-.025A5.5 5.5 0 007.207 9.021A4 4 0 007 17h2m3 4V10m0 0l-3 3m3-3l3 3" />
            </svg>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex-none flex flex-col items-center text-center gap-1 mt-4">
        <h1 class="text-sm sm:text-base font-semibold text-gray-800">
            Upload File Presensi
        </h1>

        <p class="text-[11px] sm:text-xs text-gray-500 max-w-xs leading-relaxed">
            Mendukung format file .xlsx, .xls, .csv, .ods, dan .tsv dari mesin absensi
        </p>
    </div>

    {{-- Dropzone File --}}
    <div class="flex-1 flex flex-col w-full min-h-0 relative mt-2">
        <!-- Loading -->
        <div wire:loading wire:target="file">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div class="flex flex-col items-center gap-2">
                    <x-ui.spinner />
                    <p class="text-xs text-gray-500 font-medium">
                        Mengunggah file...
                    </p>
                </div>
            </div>
        </div>

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

        <label for="dropzone-file"
            class="group relative flex-1 flex flex-col items-center justify-center w-full rounded-2xl border-2 border-dashed transition-all duration-200 cursor-pointer overflow-hidden {{ $dropzoneClass }}">

            <input wire:model="file" id="dropzone-file" type="file" accept=".xls,.xlsx,.csv,.ods,.tsv"
                class="hidden" />

            @if (!$file)
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center text-center">
                    <div
                        class="w-12 h-12 rounded-full bg-white shadow-sm border border-gray-200 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h3a3 3 0 000-6h-.025A5.5 5.5 0 007.207 9.021A4 4 0 007 17h2m3 4V10m0 0l-3 3m3-3l3 3" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mt-4">
                        Klik untuk upload file
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        atau drag & drop file ke area ini
                    </p>
                    <div class="flex flex-wrap justify-center gap-2 mt-3">
                        @foreach (['XLSX', 'XLS', 'CSV', 'ODS', 'TSV'] as $ext)
                            <span
                                class="px-2 py-1 rounded-md text-[8px] font-semibold bg-white border border-gray-200 text-gray-500">
                                {{ $ext }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-gray-400 mt-3">
                        Maksimal ukuran file 10 MB
                    </p>
                </div>
            @elseif($file)
                <!-- File Selected -->
                <div class="w-full h-full flex flex-col items-center justify-center text-center p-3">
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
                    <div
                        class="w-12 h-12 rounded-full bg-white shadow-sm border {{ $borderColor }} flex items-center justify-center mb-2">
                        <i class="fa-solid {{ $iconClass }} text-xl {{ $textColor }}"></i>
                    </div>
                    <div class="max-w-full">
                        <p class="text-xs font-semibold text-gray-700 break-all">
                            {{ $file->getClientOriginalName() }}
                        </p>
                        <p class="text-[11px] text-gray-400 mt-1">
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

    @error('file')
        <span class="mt-1 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

    {{-- Template --}}
    <button
        wire:click="downloadTemplate"
        type="button"
        class="flex-none w-full flex justify-end text-[11px] text-blue-700 hover:text-blue-600 hover:underline font-medium mr-3 cursor-pointer">
        Unduh Template
    </button>

    <button
        wire:click="save"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-75 cursor-not-allowed"
        @disabled(!$file || $errors->any())
        type="button"
        class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-full text-white font-semibold transition-all duration-300 active:scale-95 bg-gradient-to-r from-blue-500 via-indigo-500 to-pink-500
        {{ !$file || $errors->has('file')
            ? 'cursor-not-allowed opacity-60'
            : 'hover:scale-[1.01] cursor-pointer' }}
        ">

        <!-- Loading spinner -->
        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
        </svg>

        <span wire:loading.remove wire:target="save">Upload File Presensi</span>
        <span wire:loading wire:target="save">Mengupload...</span>
    </button>

</div>
