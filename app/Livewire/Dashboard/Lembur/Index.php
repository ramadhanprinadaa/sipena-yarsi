<?php

namespace App\Livewire\Dashboard\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SuratPerintahLembur;
use App\Models\Lembur;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $openDetail = false;
    public $isAutoFilled = false;
    public $lembur_items = []; // Collection untuk check status "Telah Diajukan"
    public $rekapList = [];
    public $rekapSummary = [
        'hari' => 0,
        'total_jam' => 0,
        'selesai' => 0,
        'menunggu' => 0,
    ];
    public $selectedLembur = null;

    // Filter properties
    public $filterSplDate = '';
    public $filterRiwayatDate = '';
    public $filterRekapStartDate = '';
    public $filterRekapEndDate = '';

    public function mount()
    {
        $this->loadData();
    }

    #[On('lembur-created')]
    #[On('laporan-created')]
    #[On('laporan-updated')]
    public function refreshData()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->syncDueLemburStatuses();


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

    private function durationInHours($start, $end): float
    {
        if (!$start || !$end) {
            return 0;
        }

        return Carbon::parse($start)->diffInMinutes(Carbon::parse($end)) / 60;
    }

    private function syncDueLemburStatuses(): void
    {
        Lembur::where('status', 'Menunggu Pelaksanaan')
            ->whereDate('tanggal_lembur', '<=', Carbon::today())
            ->whereDoesntHave('laporanHasilLembur')
            ->update(['status' => 'Menunggu Laporan']);

        SuratPerintahLembur::where('status', 'Diterbitkan')
            ->whereDate('tanggal_lembur', '<', Carbon::today())
            ->whereDoesntHave('lembur')
            ->update(['status' => 'Kedaluwarsa']);
    }

    public function updatedFilterSplDate()
    {
        $this->loadData();
    }

    public function updatedFilterRiwayatDate()
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

    public function render()
    {

        $user = Auth::user();

        $query = SuratPerintahLembur::query()->whereRaw('1 = 0');
        $lemburQuery = Lembur::query()->whereRaw('1 = 0');

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

            // Load semua lembur yang sudah diajukan untuk check status SPL
            $this->lembur_items = Lembur::where('pegawai_id', $user->pegawai->id)
                ->pluck('surat_perintah_lembur_id')
                ->toArray();

            // Load Riwayat Lembur Milik Pegawai
            $lemburQuery = Lembur::where('pegawai_id', $user->pegawai->id)
                ->with(['suratPerintahLembur', 'pegawai.unit_kerja', 'pegawai.user.role', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role']);

            // Apply date filter untuk Riwayat Lembur
            if ($this->filterRiwayatDate) {
                $lemburQuery->whereDate('tanggal_lembur', $this->filterRiwayatDate);
            }

            //Load Laporan Lembur Milik Pegawai
            // $this->laporanList = Lembur::where('pegawai_id', $user->pegawai->id)
            //     ->whereHas('laporanHasilLembur')
            //     ->with(['suratPerintahLembur', 'pegawai.unit_kerja', 'pegawai.user.role', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role'])
            //     ->orderBy('updated_at', 'desc')
            //     ->get();

            //Load Rekapitulasi Milik Pegawai
            $rekapQuery = Lembur::where('pegawai_id', $user->pegawai->id)
                ->where('status', 'Selesai')
                ->whereHas('laporanHasilLembur.persetujuan', function ($query) {
                    $query->where('status', 'Disetujui');
                })
                ->with(['suratPerintahLembur', 'laporanHasilLembur.persetujuan']);

            $this->applyRekapPeriodFilter($rekapQuery);

            $rekapList = $rekapQuery->orderBy('tanggal_lembur', 'desc')->get();

            $this->rekapList = $rekapList;
            $this->rekapSummary = [
                'hari' => $rekapList->count(),
                'total_jam' => $rekapList->sum(fn ($lembur) => $this->durationInHours($lembur->jam_mulai, $lembur->jam_selesai)),
                'selesai' => $rekapList->where('status', 'Selesai')->count(),
                'menunggu' => $rekapList->filter(fn ($lembur) => !in_array($lembur->status, ['Selesai', 'Ditolak']))->count(),
            ];
        }

        return view('livewire.dashboard.lembur.index', [
            'spls' => $query->paginate(10),
            'lemburList' => $lemburQuery->paginate(10),
            'laporanList' => Lembur::where('pegawai_id', $user?->pegawai?->id ?? 0)
                ->whereHas('laporanHasilLembur')
                ->with(['suratPerintahLembur', 'pegawai.unit_kerja', 'pegawai.user.role', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
        ]);
    }

    public function openAjukanModal($splId)
    {
        // Load SPL data
        $spl = SuratPerintahLembur::find($splId);

        if (!$spl) {
            return;
        }

        // Dispatch event ke AddLembur component untuk fill form
        $this->dispatch('fillFormFromSpl', [
            'splId' => $spl->id,
            'jamMulai' => $spl->jam_mulai,
            'jamSelesai' => $spl->jam_selesai,
            'tanggalLembur' => $spl->tanggal_lembur,
            'jenisHari' => $spl->jenis_hari ?? 'Hari Kerja Normal',
            'kegiatan' => $spl->nama_kegiatan,
            'alasan' => $spl->nama_kegiatan,
        ]);

        $this->dispatch('open-add-pengajuan-lembur');
    }

    public function openLaporanModal($lemburId)
    {
        // Load Lembur data
        $lembur = Lembur::find($lemburId);
        $spl = SuratPerintahLembur::find($lemburId);
        if (!$lembur) {
            return;
        }

        // Dispatch event ke AddLaporan component untuk fill form
        $this->dispatch('fillFormFromLembur', [
            'lemburId' => $lembur->id,
            'jamMulai' => $lembur->jam_mulai,
            'jamSelesai' => $lembur->jam_selesai,
            'deskripsiTugas' => $spl->deskripsi_tugas ?? '',
        ]);
        $this->dispatch('open-add-laporan-lembur');
    }

    public function showDetail($lemburId) {
        $this->selectedLembur = Lembur::with([
            'suratPerintahLembur.unitKerja',
            'pegawai.unit_kerja',
            'pegawai.user.role',
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

        $lemburQuery = Lembur::where('pegawai_id', $user?->pegawai?->id ?? 0)
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

    public function exportRekapExcel()
    {
        $data = collect($this->rekapList);
        $filename = 'Rekap_Lembur_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();

            $activeSheet->setCellValue('A1', 'Tanggal Lembur');
            $activeSheet->setCellValue('B1', 'Jenis Hari');
            $activeSheet->setCellValue('C1', 'Jam Mulai');
            $activeSheet->setCellValue('D1', 'Jam Selesai');
            $activeSheet->setCellValue('E1', 'Durasi');
            $activeSheet->setCellValue('F1', 'Status');
            $activeSheet->setCellValue('G1', 'Kegiatan');

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2B76FF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $activeSheet->getStyle('A1:G1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($data as $lembur) {
                $activeSheet->setCellValue('A' . $row, $lembur->tanggal_lembur);
                $activeSheet->setCellValue('B' . $row, $lembur->jenis_hari);
                $activeSheet->setCellValue('C' . $row, $lembur->jam_mulai);
                $activeSheet->setCellValue('D' . $row, $lembur->jam_selesai);
                $activeSheet->setCellValue('E' . $row, $this->durationInHours($lembur->jam_mulai, $lembur->jam_selesai));
                $activeSheet->setCellValue('F' . $row, $this->getStatusLembur($lembur));
                $activeSheet->setCellValue('G' . $row, $lembur->alasan_lembur);
                $row++;
            }

            foreach (range('A', 'G') as $column) {
                $activeSheet->getColumnDimension($column)->setAutoSize(true);
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

    private function initialApprovalStatusFor(Lembur $lembur): string
    {
        $role = $lembur->pegawai?->user?->role?->name;
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
        $role = $lembur->pegawai?->user?->role?->name;
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

    private function sdmVerificationStatus(Lembur $lembur): string
    {
        return ((int) $lembur->pegawai?->unit_kerja?->unit_sdm_id) === 1
            ? 'Menunggu Verifikasi SDM Yayasan'
            : 'Menunggu Verifikasi SDM Universitas';
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

        // // $approvals = $lembur->persetujuan ?? collect();

        // // if ($type === 'pengajuan' && $lembur->laporanHasilLembur) {
        // //     $approvals = $approvals->where('approved_at', '<', $lembur->laporanHasilLembur->created_at);
        // // }

        // // $approval = $approvals->sortByDesc('approved_at')->first();

        // if (!$approval || !$approval->approver) {
        //     return '-';
        // }

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