<?php

namespace App\Livewire\Manajemen\Lembur;

use Livewire\Component;
use App\Models\Lembur;
use Livewire\Attributes\On;

class DetailLaporan extends Component
{

    public $openDetailLaporan = false;
    public $selectedLemburDetail = null;

    #[On('showDetailLaporan')]
    public function showDetailLaporan(int $id): void
    {
        $this->selectedLemburDetail = Lembur::with([
            'pegawai.unit_kerja',
            'pegawai.user.role',
            'suratPerintahLembur',
            'laporanHasilLembur.persetujuan.approver.pegawai',
            'laporanHasilLembur.persetujuan.approver.role'
        ])->findOrFail($id);
        $this->openDetailLaporan = true;
    }

    public function closeDetailLaporan(): void
    {
        $this->openDetailLaporan = false;
        $this->selectedLemburDetail = null;
    }

    private function requesterRole(Lembur $lembur): ?string
    {
        return $lembur->pegawai?->user?->role?->name;
    }

    private function initialLaporanApprovalStatusFor(Lembur $lembur): string
    {
        $role = $this->requesterRole($lembur);
        $unitSdmId = (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id;

        if (!in_array($role, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])) {
            return $unitSdmId === 1
                ? 'Menunggu Verifikasi Atasan'
                : 'Menunggu Verifikasi Atasan';
        }

        if ($role === 'Pimpinan') {
            return $unitSdmId === 1
                ? 'Menunggu Verifikasi SDM Yayasan'
                : 'Menunggu Verifikasi Rektor';
        }

        if (in_array($role, ['Rektor', 'SDM Universitas'])) {
            return 'Menunggu Verifikasi SDM Yayasan';
        }

        return 'Menunggu Verifikasi SDM Yayasan';
    }

    private function getLaporanApprovalStatus(Lembur $lembur): string
    {
        $approval = $lembur->laporanHasilLembur?->persetujuan?->sortByDesc('approved_at')->first();

        if (!$approval) {
            return $this->initialLaporanApprovalStatusFor($lembur);
        }

        if ($approval->status === 'Ditolak') {
            return 'Ditolak';
        }

        if ($approval->status === 'Disetujui') {
            return 'Disetujui';
        }

        return $this->initialLaporanApprovalStatusFor($lembur);
    }

    public function getLaporanStatus(Lembur $lembur): string
    {
        if (!$lembur->laporanHasilLembur) {
            return '-';
        }

        return $this->getLaporanApprovalStatus($lembur);
    }


    public function render()
    {
        return view('livewire.manajemen.lembur.detail-laporan');
    }
}
