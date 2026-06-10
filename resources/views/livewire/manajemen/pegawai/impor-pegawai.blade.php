<div
    x-data="{ showLoading: false }"
    class="bg-white w-[45vw] max-w-[70vw] min-h-[70vh] max-h-[88vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-auto">

    <!-- Header -->
    <header class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50">
        <!-- Decoration -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        <!-- Information -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ !$showResult ? 'bg-indigo-100' : 'bg-emerald-100' }}">
                <i class="fa-solid {{ !$showResult ? 'fa-cloud-arrow-down text-indigo-500' : 'fa-circle-check  text-emerald-500' }} text-base"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    {{ !$showResult ? 'Import Data Pegawai' : 'Hasil Import Data Pegawai' }}
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <span>
                        {{ !$showResult ?
                            'Unggah file Excel atau CSV untuk import massal data pegawai' :
                            'Ringkasan dan detail hasil upload file pegawai'
                        }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Button Close -->
        <button type="button" @click="$dispatch('close-import-modal')"
            class="w-10 h-10 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all cursor-pointer">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </header>

    <!-- Content -->
    <div class="relative flex-1 overflow-y-auto">
        <!-- Loading Switch Mode -->
        <div x-show="showLoading" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
            style="display: none;">
            <div class="bg-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 border border-slate-100">
                <div class="w-6 h-6 border-2 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
                <span class="text-sm font-semibold text-slate-700 tracking-wide">Memuat Data...</span>
            </div>
        </div>

        @if (! $showResult)
            <!-- Upload Mode -->
            <div class="px-6 py-5 flex flex-1 flex-col gap-5">

                <!-- Download Template -->
                <div
                    class="flex items-center justify-between px-4 py-3 rounded-xl border border-indigo-100 bg-gradient-to-r from-[#f5f3ff] to-[#eef2ff]">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center border border-indigo-100">
                            <i class="fa-solid fa-file-lines text-md text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Template Import Pegawai</p>
                            <p class="text-[11px] text-gray-400">Gunakan template ini agar format file sesuai sistem</p>
                        </div>
                    </div>
                    <button wire:click="downloadTemplate" type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-md shadow-sm transition-all cursor-pointer hover:opacity-90 active:scale-95 bg-gradient-to-br from-[#6366f1] to-[#818cf8]">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh Template

                        <!-- Loading -->
                        <svg aria-hidden="true" wire:loading wire:target="downloadTemplate"
                            class="w-4 h-4 text-neutral-quaternary animate-spin fill-brand" viewBox="0 0 100 101"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
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
                            class="group relative flex flex-col items-center justify-center w-full min-h-[220px] rounded-2xl border-2 border-dashed transition-all duration-200 cursor-pointer overflow-hidden {{ $dropzoneClass }}">

                            <input wire:model="file" id="dropzone-file" type="file" accept=".xls,.xlsx,.csv,.ods,.tsv"
                                class="hidden" />

                            @if (!$file)
                                <!-- Empty State -->
                                <div class="flex flex-col items-center justify-center px-6 py-8 text-center">
                                    <div
                                        class="w-14 h-14 rounded-full bg-white shadow-sm border border-gray-200 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                        <svg class="w-7 h-7 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 17h3a3 3 0 000-6h-.025A5.5 5.5 0 007.207 9.021A4 4 0 007 17h2m3 4V10m0 0l-3 3m3-3l3 3" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">
                                        Klik untuk upload file
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        atau drag & drop file ke area ini
                                    </p>
                                    <div class="flex flex-wrap justify-center gap-2 mt-5">
                                        @foreach (['XLSX', 'XLS', 'CSV', 'ODS', 'TSV'] as $ext)
                                            <span
                                                class="px-2 py-1 rounded-md text-[10px] font-semibold bg-white border border-gray-200 text-gray-500">
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
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-white shadow-sm border {{ $borderColor }} flex items-center justify-center mb-2">

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
                    @foreach ([['label' => '.xlsx', 'color' => 'bg-green-100 text-green-700'], ['label' => '.xls', 'color' => 'bg-green-100 text-green-700'], ['label' => '.csv', 'color' => 'bg-blue-100 text-blue-700'], ['label' => '.ods', 'color' => 'bg-orange-100 text-orange-700'], ['label' => '.tsv', 'color' => 'bg-purple-100 text-purple-700']] as $fmt)
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $fmt['color'] }}">
                            {{ $fmt['label'] }}
                        </span>
                    @endforeach
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-gray-500 ml-auto">
                        Maks. 10 MB
                    </span>
                </div>
            </div>
        @else
            <!-- Preview Mode -->
            <div class="flex flex-col">
                <div class="flex border-b border-gray-200 px-6 pt-2 bg-white shrink-0">
                    <!-- Tab Sheet -->
                    @foreach($importResults as $sheetName => $result)
                        <button wire:click="setActiveTab('{{ $sheetName }}')"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all {{ $activeTab === $sheetName ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300' }}">
                            Sheet: {{ $sheetName }}
                        </button>
                    @endforeach
                </div>

                <div class="flex-1 overflow-y-auto bg-gray-50/50 p-6">
                    @if($activeTab && isset($importResults[$activeTab]))
                        @php $res = $importResults[$activeTab]; @endphp

                        <div class="grid grid-cols-3 gap-4 mb-6 shrink-0">
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                <p class="text-xs text-gray-500 font-semibold mb-1">Total Baris Diproses</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $res['total_rows'] }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-emerald-200 shadow-sm">
                                <p class="text-xs text-emerald-600 font-semibold mb-1">Berhasil Diimpor</p>
                                <p class="text-2xl font-bold text-emerald-600">{{ $res['total_success'] }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-red-200 shadow-sm">
                                <p class="text-xs text-red-600 font-semibold mb-1">Gagal Validasi</p>
                                <p class="text-2xl font-bold text-red-600">{{ $res['total_failed'] }}</p>
                            </div>
                        </div>

                        @if(count($res['errors']) > 0)
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Rincian Error Berdasarkan Baris Excel</h3>
                            <div class="space-y-3">
                                @foreach($res['errors'] as $message => $rows)
                                    <div x-data="{ open: false }" class="bg-white border border-red-200 rounded-lg shadow-sm">
                                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 bg-red-50/50 hover:bg-red-50 transition-colors rounded-t-lg focus:outline-none">
                                            <div class="flex items-center gap-3">
                                                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                                <span class="text-sm font-semibold text-red-700 text-left">{{ $message }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0">
                                                <span class="text-[11px] font-bold text-red-600 bg-red-100 px-2 py-1 rounded-md">{{ count($rows) }} Baris</span>
                                                <i class="fa-solid fa-chevron-down text-red-400 text-sm transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                            </div>
                                        </button>

                                        <div x-show="open" x-collapse x-cloak>
                                            <div class="p-4 border-t border-red-100 flex flex-wrap gap-2 max-h-60 overflow-y-auto">
                                                @php
                                                    $displayedRows = array_slice($rows, 0, 100);
                                                    $remaining = count($rows) - 100;
                                                @endphp

                                                @foreach($displayedRows as $row)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200 rounded">
                                                        Baris {{ $row }}
                                                    </span>
                                                @endforeach

                                                @if($remaining > 0)
                                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold bg-red-100 text-red-700 border border-red-200 rounded">
                                                        ... dan {{ $remaining }} baris lainnya
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @if($res['total_rows'] > 0)
                                <div class="flex flex-col items-center justify-center py-10 px-4 text-center bg-white rounded-xl border border-emerald-100">
                                    <div class="w-12 h-12 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-check-double text-xl"></i>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-800">Semua Data Valid!</h4>
                                    <p class="text-xs text-gray-500 mt-1">Tidak ditemukan error validasi pada sheet ini, data berhasil disimpan.</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-10 px-4 text-center bg-white rounded-xl border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-800">Sheet Kosong</h4>
                                    <p class="text-xs text-gray-500 mt-1">Tidak ada data (baris) yang terbaca pada sheet ini.</p>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/60">
        @if (! $showResult)
            <!-- Cancel Button -->
            <button type="button" @click="$dispatch('close-import-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition-all cursor-pointer">
                Batal
            </button>
            <!-- Save Button -->
            <button
                x-on:click="showLoading = true; $wire.import().finally(() => setTimeout(() => showLoading = false, 500))" wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed"
                @disabled(!$file || $errors->any()) type="button"
                class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white rounded-md shadow-sm transition-all cursor-pointer active:scale-95 {{ !$file || $errors->any() ? 'opacity-50 disabled:cursor-not-allowed' : 'hover:opacity-90' }}"
                style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);">
                <!-- Loading spinner -->
                <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                    </path>
                </svg>
                <!-- Save icon -->
                <svg wire:loading.remove wire:target="import" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span wire:loading.remove wire:target="import">Upload File</span>
                <span wire:loading wire:target="import">Menyimpan...</span>
            </button>
        @else
            <button
                x-on:click="showLoading = true; $wire.resetImport().finally(() => setTimeout(() => showLoading = false, 500))"
                type="button"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition-all cursor-pointer">
                Import Lagi
            </button>
            <button type="button" @click="$dispatch('close-import-modal')"
                class="px-4 py-2 text-sm font-semibold text-white rounded-md transition-all cursor-pointer hover:opacity-90"
                style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);">
                Tutup & Selesai
            </button>
        @endif
    </footer>

</div>
