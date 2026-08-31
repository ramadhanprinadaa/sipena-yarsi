<div>
    <!-- Modal Tambah Riwayat Pendidikan -->
    <!-- Modal dikontrol via Alpine, menangkap event Livewire open/close-add-pendidikan-modal -->
    <div
        x-data="{ show: false }"
        x-on:open-add-pendidikan-modal.window="show = true"
        x-on:close-add-pendidikan-modal.window="show = false"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4"
    >
        <!-- Klik di luar area modal akan menutup modal -->
        <div
            x-on:click.outside="show = false"
            class="w-full max-w-lg rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800"
        >
            <!-- Header Modal -->
            <div class="mb-4 flex items-center justify-between border-b pb-3 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tambah Riwayat Pendidikan
                </h3>
                <button
                    type="button"
                    wire:click="$dispatch('close-add-pendidikan-modal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                >
                    &times;
                </button>
            </div>

            <!-- Info Pegawai -->
            @if ($pegawai)
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                    Pegawai: <span class="font-medium text-gray-700 dark:text-gray-200">{{ $pegawai->nama }}</span>
                </p>
            @endif

            <!-- Form Tambah Pendidikan -->
            <form wire:submit="save" class="space-y-4">

                <!-- Field Jenjang Pendidikan -->
                <div>
                    <label for="jenjang_pendidikan_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jenjang Pendidikan
                    </label>
                    <select
                        id="jenjang_pendidikan_id"
                        wire:model="form.jenjang_pendidikan_id"
                        class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">-- Pilih Jenjang Pendidikan --</option>
                        @foreach ($jenjangPendidikan as $id => $kode)
                            <option value="{{ $id }}">{{ $kode }}</option>
                        @endforeach
                    </select>
                    @error('form.jenjang_pendidikan_id')
                        <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Field Tahun Masuk & Tahun Lulus -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tahun_masuk" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tahun Masuk
                        </label>
                        <input
                            type="text"
                            id="tahun_masuk"
                            wire:model="form.tahun_masuk"
                            maxlength="4"
                            inputmode="numeric"
                            placeholder="Contoh: 2020"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                        @error('form.tahun_masuk')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="tahun_lulus" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tahun Lulus
                        </label>
                        <input
                            type="text"
                            id="tahun_lulus"
                            wire:model="form.tahun_lulus"
                            maxlength="4"
                            inputmode="numeric"
                            placeholder="Contoh: 2024"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                        @error('form.tahun_lulus')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Field Upload File Ijazah -->
                <div>
                    <label for="file_ijazah" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        File Ijazah
                    </label>
                    <input
                        type="file"
                        id="file_ijazah"
                        wire:model="form.file_ijazah"
                        accept=".pdf,.doc,.docx"
                        class="w-full rounded-md border-gray-300 text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                    <p class="mt-1 text-xs text-gray-400">Format PDF, DOC, atau DOCX. Maksimal 10 MB.</p>

                    <!-- Indikator sedang mengunggah file -->
                    <div wire:loading wire:target="form.file_ijazah" class="mt-1 text-xs text-blue-500">
                        Mengunggah file...
                    </div>

                    @error('form.file_ijazah')
                        <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Footer / Tombol Aksi -->
                <div class="flex justify-end gap-2 border-t pt-4 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="$dispatch('close-add-pendidikan-modal')"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <!-- Teks tombol berubah saat proses simpan berjalan -->
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>