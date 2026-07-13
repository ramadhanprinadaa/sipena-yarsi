<?php

namespace App\Livewire\Dashboard\Cuti;

use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use App\Support\WorkflowEmail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddCuti extends Component
{
    use WithFileUploads;

    public $open = false;
    public $jenisCutiList = [];
    public $jenis_cuti_id = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $jam_mulai = '';
    public $jam_selesai = '';
    public $dokumen_pendukung;
    public $keterangan = '';
    public $metode_potongan = null;
    public $labelPotongCuti = 'Potong Cuti Tahunan';

    #[On('open-modal-add')]
    public function open()
    {
        $this->resetForm();
        
        $pegawai = Auth::user()?->pegawai;
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;
        $serviceYear = floor($masaKerjaBulan / 12) + 1;

        $query = JenisCuti::orderBy('id');
        if (in_array($serviceYear, [7, 8])) {
            $query->where('id', '!=', 1); // Cuti Tahunan tidak ada jika periode Cuti Besar
            $this->labelPotongCuti = 'Potong Cuti Besar';
        } else {
            $query->where('id', '!=', 2); // Cuti Besar tidak ada jika bukan periodenya
            $this->labelPotongCuti = 'Potong Cuti Tahunan';
        }

        $this->jenisCutiList = $query->get();
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
        $this->resetForm();
    }

    public function save()
    {
        $pegawai = Auth::user()?->pegawai;

        if (!$pegawai) {
            $this->addError('pegawai', 'Data pegawai tidak ditemukan.');
            return;
        }

        $jenisCuti = JenisCuti::find($this->jenis_cuti_id);
        $this->validate($this->rules($jenisCuti), [], $this->attributes());

        $tanggalMulai = Carbon::parse($this->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($this->tanggal_selesai);
        $jumlahHari = $jenisCuti->dihitung_per_jam ? null : $this->countWeekdaysBetweenDates($tanggalMulai, $tanggalSelesai);
        $jumlahJam = $jenisCuti->dihitung_per_jam ? $this->calculateHours() : null;

        if (!$this->passesBusinessRules($pegawai, $jenisCuti, $jumlahHari, $jumlahJam)) {
            return;
        }

        // --- LOGIC POTONGAN SALDO ---
        $isCutiBesarType = ($jenisCuti->id == 2);
        $isPotongCutiSakit = ($jenisCuti->id == 4 && $this->metode_potongan === 'potong_cuti');
        $harusPotongSaldo = $jenisCuti->memotong_saldo || $isPotongCutiSakit || $isCutiBesarType;

        $saldoSebelum = null;
        $saldoSesudah = null;
        $infoSaldo = null;

        if ($harusPotongSaldo && $jumlahHari) {
            $infoSaldo = $this->getSaldoCuti($pegawai, $jenisCuti);
            $saldoSebelum = $infoSaldo['sisa'];
            $saldoSesudah = $saldoSebelum;
        }

        $filePath = $this->dokumen_pendukung
            ? $this->dokumen_pendukung->store('cuti/dokumen', 'public')
            : null;

        // 1. Simpan Riwayat Pengajuan
        $cuti = Cuti::create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tanggal_pengajuan' => Carbon::today()->toDateString(),
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'jam_mulai' => $jenisCuti->dihitung_per_jam ? $this->jam_mulai : null,
            'jam_selesai' => $jenisCuti->dihitung_per_jam ? $this->jam_selesai : null,
            'jumlah_hari_cuti' => $jumlahHari,
            'jumlah_jam' => $jumlahJam,
            'metode_potongan' => ($jenisCuti->id == 4) ? $this->metode_potongan : null,
            'saldo_cuti_sebelum' => $saldoSebelum,
            'saldo_cuti_sesudah' => $saldoSesudah,
            'dokumen_pendukung' => $filePath,
            'keterangan' => $this->keterangan,
            'status' => $this->initialStatusFor($pegawai),
        ]);

        WorkflowEmail::notifyCutiSubmitted($cuti);

        $this->dispatch('cuti-created');
        $this->close();
    }

    private function rules(?JenisCuti $jenisCuti): array
    {
        $fileRule = ($jenisCuti?->butuh_surat_dokter ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:2048';

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

    private function passesBusinessRules(Pegawai $pegawai, JenisCuti $jenisCuti, ?int $jumlahHari, ?int $jumlahJam): bool
    {
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;
        
        $tanggalMulai = \Carbon\Carbon::parse($this->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($this->tanggal_selesai);

        // --- VALIDASI HARI KERJA (SENIN - JUMAT) ---
        if ($tanggalMulai->isWeekend()) {
            $this->addError('tanggal_mulai', 'Tanggal mulai cuti hanya bisa dipilih pada hari Senin - Jumat.');
            return false;
        }

        if ($tanggalSelesai->isWeekend()) {
            $this->addError('tanggal_selesai', 'Tanggal selesai cuti hanya bisa dipilih pada hari Senin - Jumat.');
            return false;
        }

        // 1. --- VALIDASI OVERLAP TANGGAL CUTI ---
        $isOverlap = Cuti::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak') // Abaikan cuti yang ditolak
            ->where('tanggal_mulai', '<=', $this->tanggal_selesai)
            ->where('tanggal_selesai', '>=', $this->tanggal_mulai)
            ->exists();

        if ($isOverlap) {
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

        // Cuti Besar Rules (Hanya cek pembatasan 33 hari per tahun)
        if ($jenisCuti->id === 2) {
            $serviceYear = floor($masaKerjaBulan / 12) + 1;
            if (!in_array($serviceYear, [7, 8])) {
                $this->addError('jenis_cuti_id', 'Cuti Besar hanya dapat diajukan pada tahun ke-7 atau ke-8 masa kerja.');
                return false;
            }

            $yearUsed = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', 2)
                ->where('status', '!=', 'ditolak')
                ->whereYear('tanggal_mulai', Carbon::parse($this->tanggal_mulai)->year)
                ->sum('jumlah_hari_cuti');

            if (($yearUsed + $jumlahHari) > 33) {
                $this->addError('tanggal_mulai', 'Pengajuan Cuti Besar di tahun yang sama tidak boleh melebihi 33 hari.');
                return false;
            }
        }
        
        // --- VALIDASI SISA SALDO (Mencakup Tahunan & Besar) ---
        $isCutiBesarType = ($jenisCuti->id == 2);
        $isPotongCutiSakit = ($jenisCuti->id == 4 && $this->metode_potongan === 'potong_cuti');
        $harusPotongSaldo = $jenisCuti->memotong_saldo || $isPotongCutiSakit || $isCutiBesarType;

        if ($harusPotongSaldo && $jumlahHari) {
            $infoSaldo = $this->getSaldoCuti($pegawai, $jenisCuti);

            if ($infoSaldo['sisa'] < $jumlahHari) {
                $this->addError('tanggal_selesai', "Sisa saldo {$infoSaldo['nama_saldo']} tidak mencukupi (Sisa: {$infoSaldo['sisa']} hari).");
                return false;
            }
        }

        //Rules Maksimal Cuti
        if ($jenisCuti->maksimal_hari && $jumlahHari > $jenisCuti->maksimal_hari) {
            $this->addError('tanggal_selesai', 'Jumlah hari melebihi maksimal cuti yang diperbolehkan.');
            return false;
        }

        //Rules Perbulan
        if ($jenisCuti->maksimal_hari_per_bulan) {
            $terpakaiBulanIni = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->whereIn('status', ['Menunggu Verifikasi Pimpinan', 'Menunggu Verifikasi Rektor', 'Menunggu Verifikasi SDM Universitas', 'Menunggu Verifikasi SDM Yayasan', 'disetujui'])
                ->whereYear('tanggal_mulai', Carbon::parse($this->tanggal_mulai)->year)
                ->whereMonth('tanggal_mulai', Carbon::parse($this->tanggal_mulai)->month)
                ->sum('jumlah_hari_cuti');

            if (($terpakaiBulanIni + $jumlahHari) > $jenisCuti->maksimal_hari_per_bulan) {
                $this->addError('tanggal_mulai', 'Pengajuan melebihi batas hari cuti dalam 1 bulan.');
                return false;
            }
        }

        //Rules Cuti Izin Menikah dan Ibadah Haji
        if ($jenisCuti->sekali_seumur_kerja) {
            $pernahMengajukan = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->where('status', '!=', 'ditolak')
                ->exists();

            if ($pernahMengajukan) {
                $this->addError('jenis_cuti_id', 'Jenis izin ini hanya dapat diajukan satu kali selama menjadi pegawai.');
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

    private function getSaldoCuti(Pegawai $pegawai, ?JenisCuti $jenisCuti = null): array
    {
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;

        $serviceYear = floor($masaKerjaBulan / 12) + 1;
        $usesCutiBesarBalance = $jenisCuti?->id == 2
            || ($jenisCuti?->id == 4 && $this->metode_potongan === 'potong_cuti' && in_array($serviceYear, [7, 8]));

        if ($usesCutiBesarBalance) {
            $totalUsed = Cuti::where('pegawai_id', $pegawai->id)
                ->where(function($query) {
                    $query->where('jenis_cuti_id', 2)
                        ->orWhere(function($q) {
                            $q->where('jenis_cuti_id', 4)->where('metode_potongan', 'potong_cuti');
                        });
                })
                ->whereIn('status', ['Menunggu Verifikasi Pimpinan', 'Menunggu Verifikasi Rektor', 'Menunggu Verifikasi SDM Universitas', 'Menunggu Verifikasi SDM Yayasan', 'disetujui'])
                ->sum('jumlah_hari_cuti');
            
            return [
                'sisa' => max(0, 66 - $totalUsed),
                'nama_saldo' => 'Cuti Besar',
                'is_cuti_besar' => true
            ];
        }

        // Kalkulasi Cuti Tahunan
        $saldo = SaldoCuti::firstOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun' => now()->year],
            ['hak_cuti' => 12, 'cuti_terpakai' => 0, 'sisa_cuti' => 12]
        );

        $reserved = Cuti::where('pegawai_id', $pegawai->id)
            ->where(function ($query) {
                $query->whereHas('jenisCuti', fn ($jenisQuery) => $jenisQuery->where('memotong_saldo', true))
                    ->orWhere(function ($q) {
                        $q->where('jenis_cuti_id', 4)->where('metode_potongan', 'potong_cuti');
                    });
            })
            ->whereIn('status', ['Menunggu Verifikasi Pimpinan', 'Menunggu Verifikasi Rektor', 'Menunggu Verifikasi SDM Universitas', 'Menunggu Verifikasi SDM Yayasan'])
            ->sum('jumlah_hari_cuti');

        return [
            'sisa' => max(0, $saldo->sisa_cuti - $reserved),
            'nama_saldo' => 'Cuti Tahunan',
            'is_cuti_besar' => false
        ];
    }

    private function initialStatusFor(Pegawai $pegawai): string
    {
        $role = $pegawai->user?->role?->name;
        $unitSdmId = (int) $pegawai->unit_kerja?->unit_sdm_id;
        $unitKerjaName = $pegawai->unit_kerja?->name ?? '';

        // 1. Role SDM Universitas dan Yayasan kini disetujui oleh Pimpinan di unitnya
        if (in_array($role, ['SDM Universitas', 'SDM Yayasan'])) {
            return 'Menunggu Verifikasi Pimpinan';
        }

        // 2. Pimpinan dari Sekretariat Universitas dialihkan ke SDM Yayasan
        if ($role === 'Pimpinan' && $unitKerjaName === 'Sekretariat Universitas') {
            return 'Menunggu Verifikasi SDM Yayasan';
        }

        // 3. Pegawai yang bernaung di bawah Yayasan (termasuk Pimpinannya)
        if ($unitSdmId === 1) {
            return 'Menunggu Verifikasi SDM Yayasan';
        }

        // 4. Pimpinan dari Universitas disetujui oleh Rektor
        if ($role === 'Pimpinan') {
            return 'Menunggu Verifikasi Rektor';
        }

        // 5. Cuti Rektor disetujui oleh SDM Universitas (Opsional, bawaan sebelumnya)
        if ($role === 'Rektor') {
            return 'Menunggu Verifikasi SDM Universitas';
        }

        // Default: Pegawai biasa di lingkungan Universitas 
        return 'Menunggu Verifikasi Pimpinan';
    }

    private function resetForm(): void
    {
        $this->reset(['jenis_cuti_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'dokumen_pendukung', 'keterangan', 'metode_potongan']);
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
            'metode_potongan' => 'opsi potongan'
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.cuti.add-cuti');
    }
}
