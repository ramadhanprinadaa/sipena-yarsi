<div class="flex flex-col gap-4 p-2 sm:p-3 lg:p-4">

    {{-- Header --}}
    <div class="flex flex-col items-center text-center gap-1">
        <h1 class="text-sm sm:text-base font-semibold text-gray-800">
            Upload File Presensi
        </h1>

        <p class="text-[11px] sm:text-xs text-gray-500 max-w-xs leading-relaxed">
            Mendukung format file XLSX, XLS, CSV, ODS, dan TSV dari mesin absensi
        </p>
    </div>

    {{-- Dropzone --}}
    <div class="relative w-full pt-6">

        {{-- Badge --}}
        <div class="absolute left-1/2 -translate-x-1/2 top-0 z-20">
            <div
                class="px-3 py-1 rounded-full bg-blue-500 text-white text-[10px] sm:text-xs font-semibold shadow-md border border-white/20">
                Import File
            </div>
        </div>

        {{-- Loading --}}
        <div wire:loading wire:target="file">
            <div
                class="absolute inset-0 z-30 rounded-2xl backdrop-blur-sm bg-black/10 flex items-center justify-center">

                <div class="flex flex-col items-center gap-2">
                    <x-ui.spinner />

                    <p class="text-xs text-gray-600 font-medium">
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

        <label
            for="dropzone-file"
            class="group relative flex flex-col items-center justify-center w-full min-h-[240px] sm:min-h-[260px] rounded-3xl border-2 border-dashed transition-all duration-300 cursor-pointer overflow-hidden px-4 py-6 {{ $dropzoneClass }}"
        >

            <input
                wire:model="file"
                id="dropzone-file"
                type="file"
                accept=".xls,.xlsx,.csv,.ods,.tsv"
                class="hidden"
            />

            @if (!$file)

                {{-- Empty State --}}
                <div class="flex flex-col items-center text-center">

                    <div
                        class="w-16 h-16 rounded-2xl bg-white shadow-sm border border-gray-200 flex items-center justify-center mb-4 transition-transform duration-300 group-hover:scale-105"
                    >
                        <svg
                            class="w-8 h-8 text-indigo-500"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 17h3a3 3 0 000-6h-.025A5.5 5.5 0 007.207 9.021A4 4 0 007 17h2m3 4V10m0 0l-3 3m3-3l3 3"
                            />
                        </svg>
                    </div>

                    <h2 class="text-sm sm:text-base font-semibold text-gray-700">
                        Klik untuk upload file
                    </h2>

                    <p class="text-[11px] sm:text-xs text-gray-400 mt-1">
                        atau drag & drop file ke area ini
                    </p>

                    <div class="flex flex-wrap justify-center gap-2 mt-5">

                        @foreach (['XLSX', 'XLS', 'CSV', 'ODS', 'TSV'] as $ext)

                            <span
                                class="px-2 py-1 rounded-md text-[10px] font-semibold bg-white border border-gray-200 text-gray-500"
                            >
                                {{ $ext }}
                            </span>

                        @endforeach

                    </div>

                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-5">
                        Maksimal ukuran file 10 MB
                    </p>

                </div>

            @else

                {{-- Selected File --}}
                <div class="flex flex-col items-center text-center">

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
                        class="w-16 h-16 rounded-2xl bg-white shadow-sm border {{ $borderColor }} flex items-center justify-center mb-3"
                    >
                        <i class="fa-solid {{ $iconClass }} text-3xl {{ $textColor }}"></i>
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

                        <span>
                            File berhasil dipilih
                        </span>
                    </div>

                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-3">
                        Klik area ini untuk mengganti file
                    </p>

                </div>

            @endif

        </label>

    </div>

    {{-- Error --}}
    @error('file')
        <span class="text-xs text-red-500 text-center">
            {{ $message }}
        </span>
    @enderror

    {{-- Template --}}
    <div class="flex justify-end">
        <a
            href=""
            class="text-[10px] sm:text-xs text-indigo-500 hover:text-indigo-600 font-medium"
        >
            Download Template
        </a>
    </div>

    {{-- Button --}}
    <button
        @disabled(!$file || $errors->has('file'))
        class="
            w-full rounded-xl py-2.5 px-4
            text-sm font-semibold text-white
            transition-all duration-300

            {{ !$file || $errors->has('file')
                ? 'bg-gray-300 cursor-not-allowed opacity-60'
                : 'bg-gradient-to-r from-blue-500 via-indigo-500 to-pink-500 hover:scale-[1.01] active:scale-[0.99]'
            }}
        "
    >
        Upload File Presensi
    </button>

</div>