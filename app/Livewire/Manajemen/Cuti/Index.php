<?php

namespace App\Livewire\Manajemen\Cuti;

use App\Models\Cuti;
use App\Models\CutiApproval;
use App\Models\SaldoCuti;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $cutiList = [];
    public $rekapList = [];
    public $jenisCutiList = [];

    public $filterRiwayatSearch = '';
    public $filterRiwayatDate = '';
    public $filterRiwayatJenis = '';
    public $filterRiwayatStatus = '';
    public $filterRekapStartDate = '';
    public $filterRekapEndDate = '';

    public $confirmAction = null;
    public $confirmId = null;
    public $confirmTitle = '';
    public $confirmMessage = '';

    public $allowedTabs = ['riwayat', 'rekap'];

    public function mount()
    {
        $this->jenisCutiList = \App\Models\JenisCuti::orderBy('id')->get();
        $this->loadData();
    }

    public function loadData()
    {
        $cutiQuery = Cuti::with(['pegawai.unit_kerja', 'pegawai.user.role', 'jenisCuti', 'approvals.approver.pegawai', 'approvals.approver.role']);
        $this->applyCutiScope($cutiQuery);

        if ($this->filterRiwayatDate) {
            $cutiQuery->whereDate('tanggal_mulai', $this->filterRiwayatDate);
        }

        if ($this->filterRiwayatJenis) {
            $cutiQuery->where('jenis_cuti_id', $this->filterRiwayatJenis);
        }

        if ($this->filterRiwayatStatus) {
            $cutiQuery->where('status', $this->filterRiwayatStatus);
        }

        if ($this->filterRiwayatSearch) {
            $cutiQuery->whereHas('pegawai', function ($query) {
                $query->where('nama', 'like', '%' . $this->filterRiwayatSearch . '%')
                    ->orWhere('nip', 'like', '%' . $this->filterRiwayatSearch . '%');
            });
        }

        $this->cutiList = $cutiQuery->orderBy('created_at', 'desc')->get();

        $rekapQuery = Cuti::with(['pegawai.unit_kerja', 'jenisCuti'])
            ->where('status', 'disetujui');
        $this->applyCutiScope($rekapQuery);
        $this->applyRekapPeriodFilter($rekapQuery);

        $this->rekapList = $rekapQuery->get()
            ->groupBy('pegawai_id')
            ->map(function ($items) {
                $first = $items->first();
                $saldo = SaldoCuti::where('pegawai_id', $first->pegawai_id)
                    ->where('tahun', now()->year)
                    ->first();

                return [
                    'nama' => $first->pegawai->nama ?? '-',
                    'nip' => $first->pegawai->nip ?? '-',
                    'jumlah_cuti' => $items->sum('jumlah_hari_cuti'),
                    'jumlah_jam' => $items->sum('jumlah_jam'),
                    'sisa_saldo' => $saldo->sisa_cuti ?? '-',
                ];
            })
            ->values();
    }

    private function scopedUnitKerjaIds(): ?array
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if ($role === 'Pimpinan') {
            return array_filter([$user->pegawai?->unit_kerja_id]);
        }

        if (in_array($role, ['Rektor', 'SDM Universitas'])) {
            return UnitKerja::where('unit_sdm_id', 2)->pluck('id')->all();
        }

        if ($role === 'SDM Yayasan') {
            return UnitKerja::whereIn('unit_sdm_id', [1, 2])->pluck('id')->all();
        }

        if ($role === 'Admin') {
            return null;
        }

        return array_filter([$user->pegawai?->unit_kerja_id]);
    }

    private function applyCutiScope($query): void
    {
        $unitKerjaIds = $this->scopedUnitKerjaIds();

        if (is_array($unitKerjaIds)) {
            $query->whereHas('pegawai', function ($pegawaiQuery) use ($unitKerjaIds) {
                $pegawaiQuery->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
            });
        }
    }

    private function applyRekapPeriodFilter($query): void
    {
        if ($this->filterRekapStartDate) {
            $query->whereDate('tanggal_mulai', '>=', $this->filterRekapStartDate);
        }

        if ($this->filterRekapEndDate) {
            $query->whereDate('tanggal_selesai', '<=', $this->filterRekapEndDate);
        }
    }

    public function updatedFilterRiwayatSearch() { $this->loadData(); }
    public function updatedFilterRiwayatDate() { $this->loadData(); }
    public function updatedFilterRiwayatJenis() { $this->loadData(); }
    public function updatedFilterRiwayatStatus() { $this->loadData(); }
    public function updatedFilterRekapStartDate() { $this->loadData(); }
    public function updatedFilterRekapEndDate() { $this->loadData(); }

    public function openApprovalConfirmation(string $action, int $id): void
    {
        $this->confirmAction = $action;
        $this->confirmId = $id;
        $this->confirmTitle = $action === 'approve' ? 'Konfirmasi Setujui' : 'Konfirmasi Tolak';
        $this->confirmMessage = 'Apakah Anda yakin ingin ' . ($action === 'approve' ? 'menyetujui' : 'menolak') . ' pengajuan cuti ini?';
    }

    public function closeApprovalConfirmation(): void
    {
        $this->reset(['confirmAction', 'confirmId', 'confirmTitle', 'confirmMessage']);
    }

    public function confirmApproval(): void
    {
        if (!$this->confirmAction || !$this->confirmId) {
            return;
        }

        $cuti = Cuti::with(['pegawai.unit_kerja', 'pegawai.user.role', 'jenisCuti'])->findOrFail($this->confirmId);

        if (!$this->canApprove($cuti)) {
            $this->closeApprovalConfirmation();
            return;
        }

        $status = $this->confirmAction === 'approve' ? 'disetujui' : 'ditolak';

        CutiApproval::create([
            'cuti_id' => $cuti->id,
            'approved_by' => Auth::id(),
            'role_approval' => Auth::user()->role->name ?? 'Pimpinan',
            'status' => $status === 'disetujui' ? 'Disetujui' : 'Ditolak',
            'catatan' => $status === 'disetujui' ? 'Disetujui' : 'Ditolak',
            'approved_at' => now(),
        ]);

        $cuti->update(['status' => $status]);

        if ($status === 'disetujui') {
            $this->applySaldoCuti($cuti);
        }

        $this->closeApprovalConfirmation();
        $this->loadData();
    }

    private function applySaldoCuti(Cuti $cuti): void
    {
        if (!$cuti->jenisCuti?->memotong_saldo || !$cuti->jumlah_hari_cuti) {
            return;
        }

        $saldo = SaldoCuti::firstOrCreate(
            ['pegawai_id' => $cuti->pegawai_id, 'tahun' => now()->year],
            ['hak_cuti' => 12, 'cuti_terpakai' => 0, 'sisa_cuti' => 12]
        );

        $saldo->update([
            'cuti_terpakai' => $saldo->cuti_terpakai + $cuti->jumlah_hari_cuti,
            'sisa_cuti' => max(0, $saldo->sisa_cuti - $cuti->jumlah_hari_cuti),
        ]);

        $cuti->update([
            'saldo_cuti_sebelum' => $saldo->sisa_cuti + $cuti->jumlah_hari_cuti,
            'saldo_cuti_sesudah' => $saldo->sisa_cuti,
        ]);
    }

    public function canApprove(Cuti $cuti): bool
    {
        $role = Auth::user()->role->name ?? '';
        $requesterRole = $cuti->pegawai?->user?->role?->name;
        $unitSdmId = (int) $cuti->pegawai?->unit_kerja?->unit_sdm_id;

        if ($role === 'Pimpinan') {
            return $cuti->status === 'pending_atasan'
                && $cuti->pegawai?->unit_kerja_id === Auth::user()->pegawai?->unit_kerja_id
                && !in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan']);
        }

        if ($role === 'Rektor') {
            return $cuti->status === 'pending_rektor'
                && $requesterRole === 'Pimpinan'
                && $unitSdmId === 2;
        }

        if ($role === 'SDM Universitas') {
            return $cuti->status === 'pending_sdm_universitas'
                && $requesterRole === 'Rektor';
        }

        if ($role === 'SDM Yayasan') {
            return $cuti->status === 'pending_sdm_yayasan'
                && ($unitSdmId === 1 || $requesterRole === 'SDM Universitas');
        }

        if ($role === 'Admin') {
            return in_array($cuti->status, ['pending_atasan', 'pending_rektor', 'pending_sdm_universitas', 'pending_sdm_yayasan']);
        }

        return false;
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'pending_atasan' => 'Menunggu Persetujuan Pimpinan',
            'pending_rektor' => 'Menunggu Persetujuan Rektor',
            'pending_sdm_universitas' => 'Menunggu Persetujuan SDM Universitas',
            'pending_sdm_yayasan' => 'Menunggu Persetujuan SDM Yayasan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $status,
        };
    }

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'disetujui' => 'bg-green-100 text-green-700',
            'ditolak' => 'bg-red-100 text-red-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    }

    public function getApproverLabel(Cuti $cuti): string
    {
        $approval = $cuti->approvals?->sortByDesc('approved_at')->first();

        if (!$approval || !$approval->approver) {
            return '-';
        }

        $nama = $approval->approver->pegawai->nama ?? $approval->approver->name ?? $approval->approver->username ?? '-';
        $role = $approval->role_approval ?? $approval->approver->role->name ?? '-';

        return $nama . ' (' . $role . ')';
    }

    public function exportRiwayatExcel()
    {
        $data = collect($this->cutiList);
        $filename = 'Riwayat_Pengajuan_Cuti_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();
            $headers = ['Nama Pegawai', 'NIP', 'Unit Kerja', 'Tanggal Pengajuan', 'Tanggal Mulai', 'Tanggal Selesai', 'Jenis Cuti', 'Jumlah', 'Sisa Saldo', 'Status', 'Disetujui Oleh'];

            foreach ($headers as $index => $header) {
                $activeSheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            $activeSheet->getStyle('A1:K1')->applyFromArray($this->headerStyle());

            $row = 2;
            foreach ($data as $cuti) {
                $activeSheet->setCellValue('A' . $row, $cuti->pegawai->nama ?? '-');
                $activeSheet->setCellValue('B' . $row, $cuti->pegawai->nip ?? '-');
                $activeSheet->setCellValue('C' . $row, $cuti->pegawai->unit_kerja->name ?? '-');
                $activeSheet->setCellValue('D' . $row, $cuti->tanggal_pengajuan);
                $activeSheet->setCellValue('E' . $row, $cuti->tanggal_mulai);
                $activeSheet->setCellValue('F' . $row, $cuti->tanggal_selesai);
                $activeSheet->setCellValue('G' . $row, $cuti->jenisCuti->nama ?? '-');
                $activeSheet->setCellValue('H' . $row, $cuti->jenisCuti?->dihitung_per_jam ? ($cuti->jumlah_jam . ' jam') : ($cuti->jumlah_hari_cuti . ' hari'));
                $activeSheet->setCellValue('I' . $row, $cuti->saldo_cuti_sesudah ?? '-');
                $activeSheet->setCellValue('J' . $row, $this->statusLabel($cuti->status));
                $activeSheet->setCellValue('K' . $row, $this->getApproverLabel($cuti));
                $row++;
            }

            foreach (range('A', 'K') as $column) {
                $activeSheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportRekapExcel()
    {
        $data = collect($this->rekapList);
        $filename = 'Rekap_Cuti_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();
            $headers = ['Nama Pegawai', 'NIP', 'Jumlah Cuti Digunakan', 'Jumlah Izin Jam', 'Sisa Saldo'];

            foreach ($headers as $index => $header) {
                $activeSheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            $activeSheet->getStyle('A1:E1')->applyFromArray($this->headerStyle());

            $row = 2;
            foreach ($data as $item) {
                $activeSheet->setCellValue('A' . $row, $item['nama']);
                $activeSheet->setCellValue('B' . $row, $item['nip']);
                $activeSheet->setCellValue('C' . $row, $item['jumlah_cuti']);
                $activeSheet->setCellValue('D' . $row, $item['jumlah_jam']);
                $activeSheet->setCellValue('E' . $row, $item['sisa_saldo']);
                $row++;
            }

            foreach (range('A', 'E') as $column) {
                $activeSheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function headerStyle(): array
    {
        return [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2B76FF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
    }

    public function render()
    {
        return view('livewire.manajemen.cuti.index', [
            'allowedTabs' => $this->allowedTabs,
        ]);
    }
}
