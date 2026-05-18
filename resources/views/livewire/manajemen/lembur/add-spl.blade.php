<div>
                <!-- BUAT & TERBITKAN SPL MODAL -->
                @if($open)
                    <div
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                    >
                        <!-- Modal Box -->
                        <div
                            class="w-full max-w-4xl max-h-[80vh] overflow-hidden bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
                        >
                            <!-- Header -->
                            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                                <h2 class="text-2xl font-bold text-[#2B76FF]">
                                    Buat & Terbitkan SPL
                                </h2>
                                <button
                                wire:click="close"
                                class="text-gray-400 hover:text-gray-600 cursor-pointer transition"
                                >
                                    <i class="fa-solid fa-xmark text-xl"></i>
                                </button>
                            </div>

                            <form wire:submit.prevent="save" class="flex flex-col flex-1 min-h-0">
                            <!-- Content Scrollable -->
                            <div class="flex-1  overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                                <div class="p-6 space-y-6">

                                    <!-- INFORMASI SURAT -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Informasi Surat</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Nomor Surat</label>
                                                <input
                                                type="text" placeholder="SPL/2023/X/089"
                                                wire:model="form.nomor_surat"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Unit Kerja</label>
                                                <input type="text" placeholder="Engineering"
                                                    wire:model="form.unit_kerja"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Dibuat</label>
                                            <input type="date"
                                                wire:model="form.tanggal_dibuat"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                        </div>
                                    </div>

                                    <!-- DETAIL KEGIATAN -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Detail Kegiatan</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Kegiatan</label>
                                            <input type="text" placeholder="Contoh: Menyelesaikan Fitur SIPENA"
                                                wire:model="form.nama_kegiatan"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition mb-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi Tugas</label>
                                            <textarea placeholder="Tuliskan rincian tugas yang harus diselesaikan..."
                                                wire:model="form.deskripsi_tugas"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50"></textarea>
                                        </div>
                                    </div>

                                    <!-- WAKTU LEMBUR -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Waktu Lembur</p>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Lembur</label>
                                                <input type="date"
                                                    wire:model="form.tanggal_lembur"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                                @error('form.tanggal_lembur')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jenis Hari</label>
                                                <select
                                                    wire:model="form.jenis_hari"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                                    <option value="">Pilih Jenis Hari</option>
                                                    <option value="Hari Kerja Normal">Hari kerja normal</option>
                                                    <option value="Hari Libur Mingguan">Hari libur mingguan</option>
                                                    <option value="Hari Libur Nasional">Hari libur nasional</option>
                                                </select>
                                                @error('form.jenis_hari')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                                <input type="time"
                                                    wire:model="form.jam_mulai"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                                @error('form.jam_mulai')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                                <input type="time"
                                                    wire:model="form.jam_selesai"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                                @error('form.jam_selesai')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PILIH PEGAWAI -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Pilih Pegawai</p>
                                        </div>

                                        <!-- Search Input -->
                                        <div class="mb-4">
                                            <div class="flex items-center border border-gray-300 rounded-lg px-3 bg-white focus-within:ring-2 focus-within:ring-[#2B76FF] focus-within:border-transparent transition">
                                                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                                                <input type="text" placeholder="Cari berdasarkan Nama, NIP, atau NPWP..."
                                                    wire:model.live.debounce.300ms="searchPegawai"
                                                    class="w-full py-2.5 px-3 text-sm outline-none bg-white focus:ring-0 focus:border-transparent border-0 focus:outline-none focus:shadow-none">
                                            </div>
                                        </div>

                                        <!-- Pegawai Table -->
                                        <div class="border border-gray-200 rounded-lg overflow-hidden max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                                            <table class="w-full text-sm">
                                                <!-- Header -->
                                                <thead class="bg-[#F5F7FA] sticky top-0 z-10">
                                                    <tr class="border-b border-gray-200">
                                                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wider">Nama Pegawai</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wider">NIP</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wider">NPWP</th>
                                                        <th class="px-4 py-3 text-center font-bold text-gray-700 text-xs uppercase tracking-wider">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <!-- Body -->
                                                <tbody class="divide-y divide-gray-200">
                                                    @forelse($pegawaiResults as $pegawai)
                                                    <tr class="hover:bg-blue-50 transition cursor-pointer">
                                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $pegawai->nama }}</td>
                                                        <td class="px-4 py-3 text-gray-600">{{ $pegawai->nip }}</td>
                                                        <td class="px-4 py-3 text-gray-600">{{ $pegawai->npwp }}</td>
                                                        <td class="px-4 py-3 text-center">
                                                            <button wire:click="selectEmployee({{ $pegawai->id }})"
                                                                class="p-1.5 rounded-lg transition cursor-pointer {{ in_array($pegawai->id, array_column($selectedEmployees, 'id')) ? 'text-white bg-[#2B76FF]' : 'text-[#2B76FF] hover:bg-blue-100' }}">
                                                                <i class="fa-solid fa-check text-sm"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">Cari pegawai untuk menampilkan hasil</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Selected Employees Display -->
                                        @if(count($selectedEmployees) > 0)
                                        <div class="mt-4">
                                            <div class="flex items-center gap-2 mb-3 pb-2 border-b-2 border-[#2B76FF]">
                                                <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                                <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Pegawai Terpilih ({{ count($selectedEmployees) }})</p>
                                            </div>
                                            <div class="space-y-2 max-h-40 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                                                @foreach($selectedEmployees as $index => $employee)
                                                <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 bg-[#2B76FF] rounded-full flex items-center justify-center">
                                                            <i class="fa-solid fa-user text-white text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-800">{{ $employee['name'] }}</p>
                                                            <p class="text-xs text-gray-600">{{ $employee['nip'] }} - {{ $employee['npwp'] }}</p>
                                                        </div>
                                                    </div>
                                                    <button wire:click="removeEmployee({{ $index }})"
                                                        class="text-red-500 hover:text-red-700 cursor-pointer transition">
                                                        <i class="fa-solid fa-xmark text-sm"></i>
                                                    </button>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <!-- Footer Button -->
                            <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                                <button
                                    type="submit"
                                    class="w-full py-3 rounded-lg text-white font-semibold
                                        bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]
                                        hover:shadow-lg cursor-pointer transition duration-300">
                                    Terbitkan SPL
                                </button>
                            </div>

                            </form>



                        </div>
                    </div>
                @endif
</div>    