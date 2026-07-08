<?php

namespace App\Livewire\Dashboard\Cuti;

use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditCuti extends Component
{
    use WithFileUploads;

    public $open = false;
    public $cutiId;
    public $jenisCutiList = [];
    public $jenis_cuti_id = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $jam_mulai = '';
    public $jam_selesai = '';
    public $dokumen_pendukung;
    public $dokumen_lama = '';
    public $keterangan = '';
    public $metode_potongan = null;

    #[On('openModalEdit')]
    public function open($id = null)
    {
        $this->resetForm();

        $pegawai = Auth::user()?->pegawai;
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;
        $serviceYear = floor($masaKerjaBulan / 12) + 1;

        $query = JenisCuti::orderBy('id');
        if (in_array($serviceYear, [7, 8])) {
            $query->where('id', '!=', 1);
        } else {
            $query->where('id', '!=', 2);
        }
        $this->jenisCutiList = $query->get();

        $this->cutiId = $id;

        $cuti = Cuti::where('pegawai_id', Auth::user()?->pegawai?->id)->findOrFail($id);

        if (in_array($cuti->status, ['disetujui', 'ditolak'])) {
            $this->addError('cuti', 'Cuti yang sudah disetujui atau ditolak tidak dapat diedit.');
            return;
        }

        $this->jenis_cuti_id = $cuti->jenis_cuti_id;
        $this->tanggal_mulai = Carbon::parse($cuti->tanggal_mulai)->format('Y-m-d');
        $this->tanggal_selesai = Carbon::parse($cuti->tanggal_selesai)->format('Y-m-d');
        $this->jam_mulai = $cuti->jam_mulai ? Carbon::parse($cuti->jam_mulai)->format('H:i') : '';
        $this->jam_selesai = $cuti->jam_selesai ? Carbon::parse($cuti->jam_selesai)->format('H:i') : '';
        $this->dokumen_lama = $cuti->dokumen_pendukung;
        $this->keterangan = $cuti->keterangan;
        $this->metode_potongan = $cuti->metode_potongan;
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
        $this->resetForm();
    }

    public function update()
    {
        $pegawai = Auth::user()?->pegawai;
        $cuti = Cuti::where('pegawai_id', $pegawai?->id)->findOrFail($this->cutiId);

        if (in_array($cuti->status, ['disetujui', 'ditolak'])) {
            $this->addError('cuti', 'Cuti yang sudah disetujui atau ditolak tidak dapat diedit.');
            return;
        }

        $jenisCuti = JenisCuti::find($this->jenis_cuti_id);

        // 1. Validasi
        $this->validate($this->rules($jenisCuti), [], $this->attributes());

        $tanggalMulai = Carbon::parse($this->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($this->tanggal_selesai);
        $jumlahHari = $jenisCuti->dihitung_per_jam ? null : $this->countWeekdaysBetweenDates($tanggalMulai, $tanggalSelesai);
        $jumlahJam = $jenisCuti->dihitung_per_jam ? $this->calculateHours() : null;

        if (!$this->passesBusinessRules($pegawai, $jenisCuti, $jumlahHari, $jumlahJam, $cuti->id)) {
            return;
        }

        $isPotongCutiSakit = ($jenisCuti->id == 4 && $this->metode_potongan === 'potong_cuti');
        $harusPotongSaldo = $jenisCuti->memotong_saldo || $isPotongCutiSakit || $jenisCuti->id == 2;

        $infoSaldo = $harusPotongSaldo && $jumlahHari
            ? $this->getSaldoCuti($pegawai, $jenisCuti, $cuti->id)
            : null;
        $saldoSebelum = $infoSaldo['sisa'] ?? null;
        $saldoSesudah = $saldoSebelum;

        // 2. Logic File Upload & Penghapusan Otomatis
        $filePath = $this->dokumen_lama;

        // Cek apakah jenis cuti yang dipilih SEKARANG butuh surat/dokumen
        if (!$jenisCuti->butuh_surat_dokter) {
            // Jika TIDAK butuh, tapi sebelumnya punya dokumen lama, hapus dari storage!
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Kosongkan path agar kolom database diperbarui menjadi null
            $filePath = null;
            $this->dokumen_lama = null; // Reset property Livewire
            $this->dokumen_pendukung = null;
        } else {
            // Jika BUTUH dokumen, cek apakah user mengunggah file baru
            if ($this->dokumen_pendukung) {
                // Hapus dokumen lama untuk diganti yang baru
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                // Simpan dokumen baru
                $filePath = $this->dokumen_pendukung->store('cuti/dokumen', 'public');
            }
        }

        // 3. Update database
        $cuti->update([
            'jenis_cuti_id' => $jenisCuti->id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'jam_mulai' => $jenisCuti->dihitung_per_jam ? $this->jam_mulai : null,
            'jam_selesai' => $jenisCuti->dihitung_per_jam ? $this->jam_selesai : null,
            'jumlah_hari_cuti' => $jumlahHari,
            'jumlah_jam' => $jumlahJam,
            'saldo_cuti_sebelum' => $saldoSebelum,
            'saldo_cuti_sesudah' => $saldoSesudah,
            'dokumen_pendukung' => $filePath, // Akan bernilai null jika jenis cuti tidak butuh dokumen
            'keterangan' => $this->keterangan,
            'metode_potongan' => ($jenisCuti->id == 4) ? $this->metode_potongan : null,
        ]);

        $this->dispatch('cuti-updated');
        $this->close();
    }

    private function rules(?JenisCuti $jenisCuti): array
    {
        // 4. Logic Validation: Dokumen hanya WAJIB jika jenis cuti butuh surat,
        // DAN pegawai belum pernah punya dokumen lama di pengajuan ini.
        $isDokumenRequired = $jenisCuti?->butuh_surat_dokter && empty($this->dokumen_lama);
        $fileRule = $isDokumenRequired
            ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';

        return [
            'jenis_cuti_id' => ['required', 'exists:jenis_cuti,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jam_mulai' => [$jenisCuti?->dihitung_per_jam ? 'required' : 'nullable', 'date_format:H:i'],
            'jam_selesai' => [$jenisCuti?->dihitung_per_jam ? 'required' : 'nullable', 'date_format:H:i', 'after:jam_mulai'],
            'dokumen_pendukung' => $fileRule,
            'keterangan' => ['required', 'string', 'min:5', 'max:1000'],
            'metode_potongan' => ['nullable', 'in:potong_gaji,potong_cuti'],
        ];
    }
    private function passesBusinessRules(Pegawai $pegawai, JenisCuti $jenisCuti, ?int $jumlahHari, ?int $jumlahJam, int $ignoreId): bool
    {
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;

        $tanggalMulai = Carbon::parse($this->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($this->tanggal_selesai);

        if ($tanggalMulai->isWeekend()) {
            $this->addError('tanggal_mulai', 'Tanggal mulai cuti hanya bisa dipilih pada hari Senin - Jumat.');
            return false;
        }

        if ($tanggalSelesai->isWeekend()) {
            $this->addError('tanggal_selesai', 'Tanggal selesai cuti hanya bisa dipilih pada hari Senin - Jumat.');
            return false;
        }
        // 1. --- VALIDASI OVERLAP TANGGAL CUTI (Khusus Edit) ---
        $overlapQuery = Cuti::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->where('tanggal_mulai', '<=', $this->tanggal_selesai)
            ->where('tanggal_selesai', '>=', $this->tanggal_mulai);

        // Kecualikan ID cuti yang sedang diedit agar tidak bertabrakan dengan dirinya sendiri
        if ($ignoreId) {
            $overlapQuery->where('id', '!=', $ignoreId);
        }

        if ($overlapQuery->exists()) {
            $this->addError('tanggal_mulai', 'Anda sudah memiliki pengajuan/riwayat cuti pada periode tanggal tersebut.');
            $this->addError('tanggal_selesai', 'Periode bertabrakan dengan cuti lain.');
            return false;
        }

        // Validasi Minimal Hari Pengajuan
        if ($jenisCuti->minimal_hari_pengajuan) {
            $diffDays = Carbon::today()->diffInDays(Carbon::parse($this->tanggal_mulai), false);
            if ($diffDays < $jenisCuti->minimal_hari_pengajuan) {
                $this->addError('tanggal_mulai', "Pengajuan jenis ini minimal dilakukan {$jenisCuti->minimal_hari_pengajuan} hari sebelum tanggal mulai.");
                return false;
            }
        }

        if ($jenisCuti->minimal_masa_kerja_bulan && $masaKerjaBulan < $jenisCuti->minimal_masa_kerja_bulan) {
            $this->addError('jenis_cuti_id', 'Masa kerja belum memenuhi syarat jenis cuti ini.');
            return false;
        }

        if ($jenisCuti->dihitung_per_jam) {
            if (!$jumlahJam || $jumlahJam < 1) {
                $this->addError('jam_selesai', 'Jam selesai harus lebih besar dari jam mulai.');
                return false;
            }

            return true;
        }

        if ($jenisCuti->id === 2) {
            $serviceYear = floor($masaKerjaBulan / 12) + 1;
            if (!in_array($serviceYear, [7, 8])) {
                $this->addError('jenis_cuti_id', 'Cuti Besar hanya dapat diajukan pada tahun ke-7 atau ke-8 masa kerja.');
                return false;
            }

            $totalUsed = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', 2)
                ->where('id', '!=', $ignoreId)
                ->where('status', '!=', 'ditolak')
                ->sum('jumlah_hari_cuti');

            if (($totalUsed + $jumlahHari) > 66) {
                $this->addError('tanggal_selesai', 'Total hak Cuti Besar adalah 66 hari.');
                return false;
            }

            $yearUsed = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', 2)
                ->where('id', '!=', $ignoreId)
                ->where('status', '!=', 'ditolak')
                ->whereYear('tanggal_mulai', Carbon::parse($this->tanggal_mulai)->year)
                ->sum('jumlah_hari_cuti');

            if (($yearUsed + $jumlahHari) > 33) {
                $this->addError('tanggal_mulai', 'Pengajuan Cuti Besar di tahun yang sama tidak boleh melebihi 33 hari.');
                return false;
            }
        }

        if ($jenisCuti->maksimal_hari && $jumlahHari > $jenisCuti->maksimal_hari) {
            $this->addError('tanggal_selesai', 'Jumlah hari melebihi maksimal cuti yang diperbolehkan.');
            return false;
        }

        if ($jenisCuti->maksimal_hari_per_bulan) {
            $terpakaiBulanIni = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->where('id', '!=', $ignoreId)
                ->whereIn('status', ['pending_atasan', 'pending_rektor', 'pending_sdm_universitas', 'pending_sdm_yayasan', 'disetujui'])
                ->whereYear('tanggal_mulai', Carbon::parse($this->tanggal_mulai)->year)
                ->whereMonth('tanggal_mulai', Carbon::parse($this->tanggal_mulai)->month)
                ->sum('jumlah_hari_cuti');

            if (($terpakaiBulanIni + $jumlahHari) > $jenisCuti->maksimal_hari_per_bulan) {
                $this->addError('tanggal_mulai', 'Pengajuan melebihi batas hari cuti dalam 1 bulan.');
                return false;
            }
        }

        if ($jenisCuti->sekali_seumur_kerja) {
            $pernahMengajukan = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->where('id', '!=', $ignoreId)
                ->where('status', '!=', 'ditolak')
                ->exists();

            if ($pernahMengajukan) {
                $this->addError('jenis_cuti_id', 'Jenis izin ini hanya dapat diajukan satu kali selama menjadi pegawai.');
                return false;
            }
        }

        $isPotongCutiSakit = ($jenisCuti->id == 4 && $this->metode_potongan === 'potong_cuti');
        $harusPotongSaldo = $jenisCuti->memotong_saldo || $isPotongCutiSakit || $jenisCuti->id == 2;

        if ($harusPotongSaldo && $jumlahHari) {
            $infoSaldo = $this->getSaldoCuti($pegawai, $jenisCuti, $ignoreId);

            if ($infoSaldo['sisa'] < $jumlahHari) {
                $this->addError('tanggal_selesai', "Sisa saldo {$infoSaldo['nama_saldo']} tidak mencukupi (Sisa: {$infoSaldo['sisa']} hari).");
                return false;
            }
        }

        return true;
    }

    private function calculateHours(): int
    {
        return (int) floor(Carbon::parse($this->jam_mulai)->diffInMinutes(Carbon::parse($this->jam_selesai)) / 60);
    }

    private function countWeekdaysBetweenDates(Carbon $startDate, Carbon $endDate): int
    {
        $count = 0;

        foreach (CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()) as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    private function getSaldoCuti(Pegawai $pegawai, ?JenisCuti $jenisCuti = null, ?int $ignoreId = null): array
    {
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;

        $serviceYear = floor($masaKerjaBulan / 12) + 1;
        $usesCutiBesarBalance = $jenisCuti?->id == 2
            || ($jenisCuti?->id == 4 && $this->metode_potongan === 'potong_cuti' && in_array($serviceYear, [7, 8]));

        if ($usesCutiBesarBalance) {
            $totalUsedQuery = Cuti::where('pegawai_id', $pegawai->id)
                ->where(function($query) {
                    $query->where('jenis_cuti_id', 2)
                        ->orWhere(function($q) {
                            $q->where('jenis_cuti_id', 4)->where('metode_potongan', 'potong_cuti');
                        });
                })
                ->whereIn('status', ['pending_atasan', 'pending_rektor', 'pending_sdm_universitas', 'pending_sdm_yayasan', 'disetujui']);

            if ($ignoreId) {
                $totalUsedQuery->where('id', '!=', $ignoreId);
            }

            $totalUsed = $totalUsedQuery->sum('jumlah_hari_cuti');

            return [
                'sisa' => max(0, 66 - $totalUsed),
                'nama_saldo' => 'Cuti Besar',
                'is_cuti_besar' => true,
            ];
        }

        $saldo = SaldoCuti::firstOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun' => now()->year],
            ['hak_cuti' => 12, 'cuti_terpakai' => 0, 'sisa_cuti' => 12]
        );

        $reservedQuery = Cuti::where('pegawai_id', $pegawai->id)
            ->where(function ($query) {
                $query->whereHas('jenisCuti', fn ($jenisQuery) => $jenisQuery->where('memotong_saldo', true))
                    ->orWhere(function ($q) {
                        $q->where('jenis_cuti_id', 4)->where('metode_potongan', 'potong_cuti');
                    });
            })
            ->whereIn('status', ['pending_atasan', 'pending_rektor', 'pending_sdm_universitas', 'pending_sdm_yayasan']);

        if ($ignoreId) {
            $reservedQuery->where('id', '!=', $ignoreId);
        }

        return [
            'sisa' => max(0, $saldo->sisa_cuti - $reservedQuery->sum('jumlah_hari_cuti')),
            'nama_saldo' => 'Cuti Tahunan',
            'is_cuti_besar' => false,
        ];
    }

    private function initialStatusFor(Pegawai $pegawai): string
    {
        $role = $pegawai->user?->role?->name;
        $unitSdmId = (int) $pegawai->unit_kerja?->unit_sdm_id;

        if ($unitSdmId === 1) {
            return 'pending_sdm_yayasan';
        }

        if ($role === 'Pimpinan') {
            return 'pending_rektor';
        }

        if ($role === 'Rektor') {
            return 'pending_sdm_universitas';
        }

        if ($role === 'SDM Universitas') {
            return 'pending_sdm_yayasan';
        }

        return 'pending_atasan';
    }

    private function resetForm(): void
    {
        $this->reset(['cutiId', 'jenis_cuti_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'dokumen_pendukung', 'dokumen_lama', 'keterangan', 'metode_potongan']);
        $this->resetErrorBag();
    }

    private function attributes(): array
    {
        return [
            'jenis_cuti_id' => 'jenis cuti',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'jam_mulai' => 'jam mulai',
            'jam_selesai' => 'jam selesai',
            'dokumen_pendukung' => 'dokumen pendukung',
            'keterangan' => 'alasan cuti',
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.cuti.edit-cuti');
    }
}