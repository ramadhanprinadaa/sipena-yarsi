<?php

namespace App\Livewire\Dashboard\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SuratPerintahLembur;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{

    public $openDetail = false;
    public $spls = [];
    public $lemburList = [];
    public $laporanList = [];
    public $selectedLembur = null;

    // Filter properties
    public $filterSplDate = '';
    public $filterRiwayatDate = '';

    public function mount()
    {
        $this->loadData();
    }

    #[On('lembur-created')]
    #[On('laporan-created')]
    public function refreshData()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->syncDueLemburStatuses();

        $user = Auth::user();
        if ($user->pegawai) {
            // Load SPL yang diterbitkan dan pegawai user termasuk di dalamnya
            $query = SuratPerintahLembur::where('status', 'Diterbitkan')
                ->whereHas('pegawai', function ($query) use ($user) {
                    $query->where('pegawai.id', $user->pegawai->id);
                })
                ->with(['pegawai', 'unitKerja']);

            // Apply date filter untuk SPL
            if ($this->filterSplDate) {
                $query->whereDate('tanggal_lembur', $this->filterSplDate);
            }

            $this->spls = $query->get();

            // Load riwayat lembur milik pegawai user
            $lemburQuery = Lembur::where('pegawai_id', $user->pegawai->id)
                ->with(['suratPerintahLembur', 'pegawai', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role']);

            // Apply date filter untuk Riwayat Lembur
            if ($this->filterRiwayatDate) {
                $lemburQuery->whereDate('tanggal_lembur', $this->filterRiwayatDate);
            }

            $this->lemburList = $lemburQuery->get();

            $this->laporanList = Lembur::where('pegawai_id', $user->pegawai->id)
                ->whereHas('laporanHasilLembur')
                ->with(['suratPerintahLembur', 'pegawai', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role'])
                ->orderBy('updated_at', 'desc')
                ->get();
        }
    }

    private function syncDueLemburStatuses(): void
    {
        Lembur::where('status', 'Menunggu Pelaksanaan')
            ->whereDate('tanggal_lembur', '<=', Carbon::today())
            ->whereDoesntHave('laporanHasilLembur')
            ->update(['status' => 'Menunggu Laporan']);
    }

    public function updatedFilterSplDate()
    {
        $this->loadData();
    }

    public function updatedFilterRiwayatDate()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.dashboard.lembur.index');
    }

    public function showDetail($lemburId) {
        $this->selectedLembur = Lembur::with([
            'suratPerintahLembur.unitKerja',
            'pegawai',
            'laporanHasilLembur.persetujuan.approver.pegawai',
            'laporanHasilLembur.persetujuan.approver.role',
            'persetujuan.approver.pegawai',
            'persetujuan.approver.role'
            ])->find($lemburId);
        $this->openDetail = true;
    }

    public function closeDetail() {
        $this->openDetail = false;
        $this->selectedLembur = null;
    }

    public function exportRiwayatExcel()
    {
        $user = Auth::user();
        if (!$user->pegawai) {
            return;
        }

        $lemburQuery = Lembur::where('pegawai_id', $user->pegawai->id)
            ->with(['suratPerintahLembur', 'pegawai']);

        if ($this->filterRiwayatDate) {
            $lemburQuery->whereDate('tanggal_lembur', $this->filterRiwayatDate);
        }

        $data = $lemburQuery->get();

        // Create Excel file
        $filename = 'Riwayat_Lembur_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();

            // Header
            $activeSheet->setCellValue('A1', 'Tanggal Lembur');
            $activeSheet->setCellValue('B1', 'Jenis Hari');
            $activeSheet->setCellValue('C1', 'Jam Mulai');
            $activeSheet->setCellValue('D1', 'Jam Selesai');
            $activeSheet->setCellValue('E1', 'Kegiatan');
            $activeSheet->setCellValue('F1', 'Status');

            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2B76FF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $activeSheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            // Data
            $row = 2;
            foreach ($data as $lembur) {
                $activeSheet->setCellValue('A' . $row, $lembur->tanggal_lembur);
                $activeSheet->setCellValue('B' . $row, $lembur->jenis_hari);
                $activeSheet->setCellValue('C' . $row, $lembur->jam_mulai);
                $activeSheet->setCellValue('D' . $row, $lembur->jam_selesai);
                $activeSheet->setCellValue('E' . $row, $lembur->alasan_lembur);
                $activeSheet->setCellValue('F' . $row, $lembur->status);
                $row++;
            }

            // Auto column width
            foreach (range('A', 'F') as $col) {
                $activeSheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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

        return match ($lembur->status) {
            'Selesai' => 'Disetujui',
            'Ditolak' => 'Ditolak',
            default => 'Menunggu Verifikasi Atasan',
        };
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

}