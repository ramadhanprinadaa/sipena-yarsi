<?php

namespace App\Livewire\Dashboard\Cuti;

use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Carbon\Carbon;
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

    #[On('openModalEdit')]
    public function open($id = null)
    {
        $this->resetForm();
        $this->jenisCutiList = JenisCuti::orderBy('id')->get();
        $this->cutiId = $id;

        $cuti = Cuti::where('pegawai_id', Auth::user()?->pegawai?->id)->findOrFail($id);

        if ($cuti->status === 'disetujui') {
            $this->addError('cuti', 'Cuti yang sudah disetujui tidak dapat diedit.');
            return;
        }

        $this->jenis_cuti_id = $cuti->jenis_cuti_id;
        $this->tanggal_mulai = Carbon::parse($cuti->tanggal_mulai)->format('Y-m-d');
        $this->tanggal_selesai = Carbon::parse($cuti->tanggal_selesai)->format('Y-m-d');
        $this->jam_mulai = $cuti->jam_mulai ? Carbon::parse($cuti->jam_mulai)->format('H:i') : '';
        $this->jam_selesai = $cuti->jam_selesai ? Carbon::parse($cuti->jam_selesai)->format('H:i') : '';
        $this->dokumen_lama = $cuti->dokumen_pendukung;
        $this->keterangan = $cuti->keterangan;
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

        if ($cuti->status === 'disetujui') {
            $this->addError('cuti', 'Cuti yang sudah disetujui tidak dapat diedit.');
            return;
        }

        $jenisCuti = JenisCuti::find($this->jenis_cuti_id);
        $this->validate($this->rules($jenisCuti), [], $this->attributes());

        $tanggalMulai = Carbon::parse($this->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($this->tanggal_selesai);
        $jumlahHari = $jenisCuti->dihitung_per_jam ? null : $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $jumlahJam = $jenisCuti->dihitung_per_jam ? $this->calculateHours() : null;

        if (!$this->passesBusinessRules($pegawai, $jenisCuti, $jumlahHari, $jumlahJam, $cuti->id)) {
            return;
        }

        $saldo = $this->getSaldoCuti($pegawai);
        $saldoSebelum = $saldo?->sisa_cuti ?? null;
        $saldoSesudah = $jenisCuti->memotong_saldo && $jumlahHari
            ? max(0, ($saldoSebelum ?? 0) - $jumlahHari)
            : $saldoSebelum;

        $filePath = $cuti->dokumen_pendukung;
        if ($this->dokumen_pendukung) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            $filePath = $this->dokumen_pendukung->store('cuti/dokumen', 'public');
        }

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
            'dokumen_pendukung' => $filePath,
            'keterangan' => $this->keterangan,
            'status' => $this->initialStatusFor($pegawai),
        ]);

        $this->dispatch('cuti-updated');
        $this->close();
    }

    private function rules(?JenisCuti $jenisCuti): array
    {
        $fileRule = ($jenisCuti?->butuh_surat_dokter && !$this->dokumen_lama ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:2048';

        return [
            'jenis_cuti_id' => ['required', 'exists:jenis_cuti,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jam_mulai' => [$jenisCuti?->dihitung_per_jam ? 'required' : 'nullable', 'date_format:H:i'],
            'jam_selesai' => [$jenisCuti?->dihitung_per_jam ? 'required' : 'nullable', 'date_format:H:i', 'after:jam_mulai'],
            'dokumen_pendukung' => $fileRule,
            'keterangan' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    private function passesBusinessRules(Pegawai $pegawai, JenisCuti $jenisCuti, ?int $jumlahHari, ?int $jumlahJam, int $ignoreId): bool
    {
        $masaKerjaBulan = $pegawai->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;

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

        if ($jenisCuti->memotong_saldo && ($this->getSaldoCuti($pegawai)?->sisa_cuti ?? 0) < $jumlahHari) {
            $this->addError('tanggal_selesai', 'Sisa saldo cuti tidak mencukupi.');
            return false;
        }

        return true;
    }

    private function calculateHours(): int
    {
        return (int) floor(Carbon::parse($this->jam_mulai)->diffInMinutes(Carbon::parse($this->jam_selesai)) / 60);
    }

    private function getSaldoCuti(Pegawai $pegawai): ?SaldoCuti
    {
        return SaldoCuti::firstOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun' => now()->year],
            ['hak_cuti' => 12, 'cuti_terpakai' => 0, 'sisa_cuti' => 12]
        );
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
        $this->reset(['cutiId', 'jenis_cuti_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'dokumen_pendukung', 'dokumen_lama', 'keterangan']);
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
