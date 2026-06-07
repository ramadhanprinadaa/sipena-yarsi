<?php

namespace App\Livewire\Manajemen\Lembur;

use Livewire\Component;
use App\Models\SuratPerintahLembur;
use App\Models\Lembur;
use App\Models\PersetujuanLembur;
use App\Models\PersetujuanLaporan;
use App\Models\UnitKerja;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $spls = [];
    public $lemburList = [];
    public $laporanList = [];
    public $rekapList = [];
    public $confirmAction = null;
    public $confirmType = null;
    public $confirmId = null;
    public $confirmTitle = '';
    public $confirmMessage = '';

    // Filter properties
    public $filterSplDate = '';
    public $filterRiwayatDate = '';
    public $filterRiwayatStatus = '';
    public $filterRiwayatSearch = '';
    public $filterLaporanDate = '';
    public $filterLaporanSearch = '';
    public $filterRekapStartDate = '';
    public $filterRekapEndDate = '';

    public $allowedTabs = ['spl', 'riwayat'];

    public function mount()
    {
        $this->determineAllowedTabs();
        $this->loadData();
    }

    public function determineAllowedTabs()
    {
        $user = Auth::user();
        $userRole = $user->role->name ?? '';

        // Pimpinan & Rektor dapat melihat SPL & Riwayat
        if (in_array($userRole, ['Pimpinan', 'Rektor'])) {
            $this->allowedTabs = ['spl', 'riwayat', 'rekap', 'verifikasi'];
        } elseif (in_array($userRole, ['SDM Yayasan', 'SDM Universitas'])) {
            $this->allowedTabs = ['spl', 'riwayat', 'rekap', 'verifikasi'];
        } else {
            $this->allowedTabs = ['riwayat'];
        }
    }

    public function loadData()
    {
        $this->syncDueLemburStatuses();

        // Load SPL untuk Pimpinan
        if (in_array('spl', $this->allowedTabs)) {
            $splQuery = SuratPerintahLembur::with(['pegawai', 'unitKerja']);

            $this->applySplScope($splQuery);

            if ($this->filterSplDate) {
                $splQuery->whereDate('tanggal_lembur', $this->filterSplDate);
            }

            $this->spls = $splQuery->orderBy('created_at', 'desc')->get();
        }

        // Load Riwayat Pengajuan Lembur para pegawai
        if (in_array('riwayat', $this->allowedTabs)) {
            $lemburQuery = Lembur::with(['pegawai.unit_kerja', 'pegawai.user.role', 'suratPerintahLembur', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role']);

            $this->applyLemburScope($lemburQuery);

            // Apply date filter
            if ($this->filterRiwayatDate) {
                $lemburQuery->whereDate('tanggal_lembur', $this->filterRiwayatDate);
            }

            // Apply status filter
            if ($this->filterRiwayatStatus) {
                $lemburQuery->where('status', $this->filterRiwayatStatus);
            }

            // Apply search filter
            if ($this->filterRiwayatSearch) {
                $lemburQuery->whereHas('pegawai', function ($query) {
                    $query->where('nama', 'like', '%' . $this->filterRiwayatSearch . '%')
                        ->orWhere('nip', 'like', '%' . $this->filterRiwayatSearch . '%');
                });
            }

            $this->lemburList = $lemburQuery->orderBy('created_at', 'desc')->get();
        }

        if (in_array('verifikasi', $this->allowedTabs)) {
            $laporanQuery = Lembur::whereHas('laporanHasilLembur')
                ->with(['pegawai.unit_kerja', 'pegawai.user.role', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'suratPerintahLembur', 'persetujuan.approver.pegawai', 'persetujuan.approver.role']);

            $this->applyLemburScope($laporanQuery);

            if ($this->filterLaporanDate) {
                $laporanQuery->whereDate('tanggal_lembur', $this->filterLaporanDate);
            }

            if ($this->filterLaporanSearch) {
                $laporanQuery->whereHas('pegawai', function ($query) {
                    $query->where('nama', 'like', '%' . $this->filterLaporanSearch . '%')
                        ->orWhere('nip', 'like', '%' . $this->filterLaporanSearch . '%');
                });
            }

            $this->laporanList = $laporanQuery->orderBy('updated_at', 'desc')->get();
        }

        if (in_array('rekap', $this->allowedTabs)) {
            $rekapQuery = Lembur::with('pegawai');
            $this->applyLemburScope($rekapQuery);
            $this->applyRekapPeriodFilter($rekapQuery);

            $this->rekapList = $rekapQuery->get()
                ->groupBy('pegawai_id')
                ->map(function ($items) {
                    $first = $items->first();

                    return [
                        'nama' => $first->pegawai->nama ?? '-',
                        'nip' => $first->pegawai->nip ?? '-',
                        'hari' => $items->count(),
                        'total_jam' => $items->sum(function ($lembur) {
                            $start = Carbon::parse($lembur->jam_mulai);
                            $end = Carbon::parse($lembur->jam_selesai);

                            return $start->diffInMinutes($end) / 60;
                        }),
                    ];
                })
                ->values();
        }
    }

    private function syncDueLemburStatuses(): void
    {
        Lembur::where('status', 'Menunggu Pelaksanaan')
            ->whereDate('tanggal_lembur', '<=', Carbon::today())
            ->whereDoesntHave('laporanHasilLembur')
            ->update(['status' => 'Menunggu Laporan']);
    }

    private function scopedUnitKerjaIds(): ?array
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if ($role === 'Pimpinan') {
            return array_filter([$user->pegawai?->unit_kerja_id]);
        }

        if ($role === 'Rektor') {
            return UnitKerja::where('unit_sdm_id', 2)->pluck('id')->all();
        }

        if ($role === 'SDM Universitas') {
            return UnitKerja::where('unit_sdm_id', 2)->pluck('id')->all();
        }

        if ($role === 'SDM Yayasan') {
            return UnitKerja::whereIn('unit_sdm_id', [1, 2])->pluck('id')->all();
        }

        if (in_array($role, ['Admin', 'Rektor'])) {
            return null;
        }

        return array_filter([$user->pegawai?->unit_kerja_id]);
    }

    private function applySplScope($query): void
    {
        $unitKerjaIds = $this->scopedUnitKerjaIds();

        if (is_array($unitKerjaIds)) {
            $query->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
        }
    }

    private function applyLemburScope($query): void
    {
        $unitKerjaIds = $this->scopedUnitKerjaIds();

        if (is_array($unitKerjaIds)) {
            $query->whereHas('pegawai', function ($pegawaiQuery) use ($unitKerjaIds) {
                $pegawaiQuery->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
            });
        }
    }

    public function updatedFilterSplDate()
    {
        $this->loadData();
    }

    public function updatedFilterRiwayatDate()
    {
        $this->loadData();
    }

    public function updatedFilterRiwayatStatus()
    {
        $this->loadData();
    }

    public function updatedFilterRiwayatSearch()
    {
        $this->loadData();
    }

    public function updatedFilterLaporanDate()
    {
        $this->loadData();
    }

    public function updatedFilterLaporanSearch()
    {
        $this->loadData();
    }

    public function updatedFilterRekapStartDate()
    {
        $this->loadData();
    }

    public function updatedFilterRekapEndDate()
    {
        $this->loadData();
    }

    private function applyRekapPeriodFilter($query): void
    {
        if ($this->filterRekapStartDate) {
            $query->whereDate('tanggal_lembur', '>=', $this->filterRekapStartDate);
        }

        if ($this->filterRekapEndDate) {
            $query->whereDate('tanggal_lembur', '<=', $this->filterRekapEndDate);
        }
    }

    public function openApprovalConfirmation(string $type, string $action, int $id): void
    {
        $this->confirmType = $type;
        $this->confirmAction = $action;
        $this->confirmId = $id;

        $label = $action === 'approve' ? 'Setujui' : 'Tolak';
        $target = $type === 'laporan' ? 'Laporan Lembur' : 'Pengajuan Lembur';

        $this->confirmTitle = 'Konfirmasi ' . $label;
        $this->confirmMessage = 'Apakah Anda yakin ingin ' . strtolower($label) . ' ' . $target . ' ini?';
    }

    public function closeApprovalConfirmation(): void
    {
        $this->reset(['confirmAction', 'confirmType', 'confirmId', 'confirmTitle', 'confirmMessage']);
    }

    public function confirmApproval(): void
    {
        if (!$this->confirmType || !$this->confirmAction || !$this->confirmId) {
            return;
        }

        if ($this->confirmType === 'laporan') {
            $this->processLaporanApproval($this->confirmId, $this->confirmAction);
        } else {
            $this->processPengajuanApproval($this->confirmId, $this->confirmAction);
        }

        $this->closeApprovalConfirmation();
        $this->loadData();
    }

    private function processPengajuanApproval(int $lemburId, string $action): void
    {
        $lembur = Lembur::with(['pegawai.unit_kerja', 'pegawai.user.role'])->findOrFail($lemburId);

        if (!$this->canApprovePengajuan($lembur) || $lembur->laporanHasilLembur) {
            return;
        }

        $lembur->update([
            'status' => $this->nextPengajuanStatus($lembur, $action),
        ]);

        $this->recordApproval($lembur->id, $action, $action === 'approve' ? 'Pengajuan Lembur Disetujui' : 'Pengajuan Lembur Ditolak');
    }

    private function processLaporanApproval(int $lemburId, string $action): void
    {
        $lembur = Lembur::with(['pegawai.unit_kerja', 'pegawai.user.role', 'laporanHasilLembur.persetujuan'])->findOrFail($lemburId);

        if (!$lembur->laporanHasilLembur || !$this->canApproveLaporan($lembur)) {
            return;
        }

        $this->recordLaporanApproval($lembur->laporanHasilLembur->id, $action, $action === 'approve' ? 'Disetujui' : 'Ditolak');

        if ($action === 'approve' && in_array(Auth::user()->role->name ?? '', ['SDM Universitas', 'SDM Yayasan'])) {
            $lembur->update([
                'status' => 'Selesai',
            ]);
        }
    }

    private function recordApproval(int $lemburId, string $action, string $catatan): void
    {
        $user = Auth::user();

        PersetujuanLembur::create([
            'lembur_id' => $lemburId,
            'approved_by' => $user->id,
            'role_approval' => $user->role->name ?? 'Pimpinan',
            'status' => $action === 'approve' ? 'Disetujui' : 'Ditolak',
            'catatan' => $catatan,
            'approved_at' => now(),
        ]);
    }

    private function recordLaporanApproval(int $laporanHasilLemburId, string $action, string $catatan): void
    {
        $user = Auth::user();

        PersetujuanLaporan::create([
            'laporan_hasil_lembur_id' => $laporanHasilLemburId,
            'approved_by' => $user->id,
            'role_approval' => $user->role->name ?? 'Pimpinan',
            'status' => $action === 'approve' ? 'Disetujui' : 'Ditolak',
            'catatan' => $catatan,
            'approved_at' => now(),
        ]);
    }

    public function canApprovePengajuan(Lembur $lembur): bool
    {
        $role = Auth::user()->role->name ?? '';

        if ($role === 'Pimpinan') {
            return $lembur->status === 'Menunggu Verifikasi Atasan'
                && $lembur->pegawai?->unit_kerja_id === Auth::user()->pegawai?->unit_kerja_id
                && !in_array($this->requesterRole($lembur), ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan']);
        }

        if ($role === 'Rektor') {
            return $lembur->status === 'Menunggu Verifikasi Rektor'
                && $this->requesterRole($lembur) === 'Pimpinan'
                && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 2;
        }

        if ($role === 'SDM Universitas') {
            return $lembur->status === 'Menunggu Verifikasi SDM Universitas'
                && $this->requesterRole($lembur) === 'Rektor';
        }

        if ($role === 'SDM Yayasan') {
            return $lembur->status === 'Menunggu Verifikasi SDM Yayasan'
                && in_array($this->requesterRole($lembur), ['Pimpinan', 'SDM Universitas']);
        }

        return false;
    }

    public function canApproveLaporan(Lembur $lembur): bool
    {
        if (!$lembur->laporanHasilLembur) {
            return false;
        }

        $role = Auth::user()->role->name ?? '';
        $approvalStatus = $this->getLaporanApprovalStatus($lembur);
        $requesterRole = $this->requesterRole($lembur);

        if ($role === 'SDM Universitas') {
            return $approvalStatus === 'Menunggu Verifikasi SDM Universitas'
                && (
                    (!in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 2)
                    || ($requesterRole === 'Pimpinan'
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 2)
                );
        }

        if ($role === 'SDM Yayasan') {
            return $approvalStatus === 'Menunggu Verifikasi SDM Yayasan'
                && (
                    (!in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 1)
                    || ($requesterRole === 'Pimpinan'
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 1)
                    || in_array($requesterRole, ['Rektor', 'SDM Universitas'])
                );
        }

        return false;
    }

    private function nextPengajuanStatus(Lembur $lembur, string $action): string
    {
        if ($action !== 'approve') {
            return 'Ditolak';
        }

        return 'Menunggu Pelaksanaan';
    }

    public function initialApprovalStatusFor(Lembur $lembur): string
    {
        $role = $this->requesterRole($lembur);
        $unitSdmId = (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id;

        if ($role === 'Pimpinan') {
            return $unitSdmId === 1
                ? 'Menunggu Verifikasi SDM Yayasan'
                : 'Menunggu Verifikasi Rektor';
        }

        if ($role === 'Rektor') {
            return 'Menunggu Verifikasi SDM Universitas';
        }

        if ($role === 'SDM Universitas') {
            return 'Menunggu Verifikasi SDM Yayasan';
        }

        return 'Menunggu Verifikasi Atasan';
    }

    private function initialLaporanApprovalStatusFor(Lembur $lembur): string
    {
        $role = $this->requesterRole($lembur);
        $unitSdmId = (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id;

        if (!in_array($role, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])) {
            return $unitSdmId === 1
                ? 'Menunggu Verifikasi SDM Yayasan'
                : 'Menunggu Verifikasi SDM Universitas';
        }

        if ($role === 'Pimpinan') {
            return $unitSdmId === 1
                ? 'Menunggu Verifikasi SDM Yayasan'
                : 'Menunggu Verifikasi SDM Universitas';
        }

        if (in_array($role, ['Rektor', 'SDM Universitas'])) {
            return 'Menunggu Verifikasi SDM Yayasan';
        }

        return 'Menunggu Verifikasi SDM Yayasan';
    }

    private function requesterRole(Lembur $lembur): ?string
    {
        return $lembur->pegawai?->user?->role?->name;
    }

    private function sdmVerificationStatus(Lembur $lembur): string
    {
        return ((int) $lembur->pegawai?->unit_kerja?->unit_sdm_id) === 1
            ? 'Menunggu Verifikasi SDM Yayasan'
            : 'Menunggu Verifikasi SDM Universitas';
    }

    public function getStatusLembur(Lembur $lembur): string
    {
        if ($lembur->status === 'Menunggu Pelaksanaan' && Carbon::parse($lembur->tanggal_lembur)->lte(Carbon::today()) && !$lembur->laporanHasilLembur) {
            return 'Menunggu Laporan';
        }

        return $lembur->status;
    }

    public function getLaporanStatus(Lembur $lembur): string
    {
        if (!$lembur->laporanHasilLembur) {
            return '-';
        }

        return $this->getLaporanApprovalStatus($lembur);
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

    public function getApproverLabel(Lembur $lembur, string $type = 'latest'): string
    {
        if ($type === 'laporan') {
            $approval = $lembur->laporanHasilLembur?->persetujuan?->sortByDesc('approved_at')->first();

            if (!$approval || !$approval->approver) {
                return '-';
            }

            $nama = $approval->approver->pegawai->nama ?? $approval->approver->name ?? $approval->approver->username ?? '-';
            $role = $approval->role_approval ?? $approval->approver->role->name ?? '-';

            return $nama . ' (' . $role . ')';
        }

        $approvals = $lembur->persetujuan ?? collect();

        if ($type === 'pengajuan' && $lembur->laporanHasilLembur) {
            $approvals = $approvals->where('approved_at', '<', $lembur->laporanHasilLembur->created_at);
        }

        $approval = $approvals->sortByDesc('approved_at')->first();

        if (!$approval || !$approval->approver) {
            return '-';
        }

        $nama = $approval->approver->pegawai->nama ?? $approval->approver->name ?? $approval->approver->username ?? '-';
        $role = $approval->role_approval ?? $approval->approver->role->name ?? '-';

        return $nama . ' (' . $role . ')';
    }

    public function getApprovalCatatan(Lembur $lembur, string $type = 'latest'): string
    {
        if ($type === 'laporan') {
            $approval = $lembur->laporanHasilLembur?->persetujuan?->sortByDesc('approved_at')->first();

            return $approval->catatan ?? '-';
        }

        $approvals = $lembur->persetujuan ?? collect();

        if ($type === 'pengajuan' && $lembur->laporanHasilLembur) {
            $approvals = $approvals->where('approved_at', '<', $lembur->laporanHasilLembur->created_at);
        }

        $approval = $approvals->sortByDesc('approved_at')->first();

        return $approval->catatan ?? '-';
    }

    #[On(['spl-created', 'spl-updated'])]
    public function refreshData()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.manajemen.lembur.index', [
            'allowedTabs' => $this->allowedTabs,
        ]);
    }

    public function exportRiwayatExcel()
    {
        $data = collect($this->lemburList);
        $filename = 'Riwayat_Lembur_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();

            $activeSheet->setCellValue('A1', 'Nama Pegawai');
            $activeSheet->setCellValue('B1', 'NIP');
            $activeSheet->setCellValue('C1', 'Unit Kerja');
            $activeSheet->setCellValue('D1', 'Tanggal Lembur');
            $activeSheet->setCellValue('E1', 'Jenis Hari');
            $activeSheet->setCellValue('F1', 'Jam Mulai');
            $activeSheet->setCellValue('G1', 'Jam Selesai');
            $activeSheet->setCellValue('H1', 'Kegiatan');
            $activeSheet->setCellValue('I1', 'Status');
            $activeSheet->setCellValue('J1', 'Disetujui Oleh');

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2B76FF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $activeSheet->getStyle('A1:J1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($data as $lembur) {
                $activeSheet->setCellValue('A' . $row, $lembur->pegawai->nama ?? '-');
                $activeSheet->setCellValue('B' . $row, $lembur->pegawai->nip ?? '-');
                $activeSheet->setCellValue('C' . $row, $lembur->pegawai->unit_kerja->name ?? '-');
                $activeSheet->setCellValue('D' . $row, $lembur->tanggal_lembur);
                $activeSheet->setCellValue('E' . $row, $lembur->jenis_hari);
                $activeSheet->setCellValue('F' . $row, $lembur->jam_mulai);
                $activeSheet->setCellValue('G' . $row, $lembur->jam_selesai);
                $activeSheet->setCellValue('H' . $row, $lembur->alasan_lembur);
                $activeSheet->setCellValue('I' . $row, $this->getStatusLembur($lembur));
                $activeSheet->setCellValue('J' . $row, $this->getApproverLabel($lembur, 'pengajuan'));
                $row++;
            }

            foreach (range('A', 'J') as $column) {
                $activeSheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportRekapExcel()
    {
        $data = collect($this->rekapList);
        $filename = 'Rekap_Lembur_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();

            $activeSheet->setCellValue('A1', 'Nama Pegawai');
            $activeSheet->setCellValue('B1', 'NIP');
            $activeSheet->setCellValue('C1', 'Periode Mulai');
            $activeSheet->setCellValue('D1', 'Periode Selesai');
            $activeSheet->setCellValue('E1', 'Hari (Jumlah)');
            $activeSheet->setCellValue('F1', 'Total Jam');

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2B76FF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $activeSheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($data as $item) {
                $activeSheet->setCellValue('A' . $row, $item['nama']);
                $activeSheet->setCellValue('B' . $row, $item['nip']);
                $activeSheet->setCellValue('C' . $row, $this->filterRekapStartDate ?: '-');
                $activeSheet->setCellValue('D' . $row, $this->filterRekapEndDate ?: '-');
                $activeSheet->setCellValue('E' . $row, $item['hari']);
                $activeSheet->setCellValue('F' . $row, $item['total_jam']);
                $row++;
            }

            foreach (range('A', 'F') as $column) {
                $activeSheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}