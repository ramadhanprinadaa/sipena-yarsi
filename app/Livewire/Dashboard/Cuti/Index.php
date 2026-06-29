<?php

namespace App\Livewire\Dashboard\Cuti;

use App\Models\Cuti;
use App\Models\SaldoCuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public $rekapList = [];
    public $labelSaldoCuti = '';
    public $sisaSaldoCuti = 0;
    public $cutiTerpakai = 0;
    public $cutiTerpakaiTahunan = 0;
    public $cutiTerpakaiBesar = 0;
    public $rekapSummary = [
        'sisa_saldo' => 0,
        'label_saldo_cuti' => '',
        'cuti_terpakai' => 0,
        'cuti_terpakai_tahunan' => 0,
        'cuti_terpakai_besar' => 0,
        'cuti_terpakai_melahirkan' => 0,
        'cuti_terpakai_total' => 0,
    ];

    public $filterRiwayatDate = '';
    public $filterRiwayatStatus = '';
    public $filterRekapStartDate = '';
    public $filterRekapEndDate = '';

    public function mount()
    {
        $this->loadData();
    }

    #[On('cuti-created')]
    #[On('cuti-updated')]
    #[On('cuti-deleted')]
    public function refreshData()
    {
        $this->loadData();
    }

    public function loadData()
    {
        

        
    }

    public function updatedFilterRiwayatDate()
    {
        $this->loadData();
    }

    public function updatedFilterRiwayatStatus()
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
            $query->whereDate('tanggal_mulai', '>=', $this->filterRekapStartDate);
        }

        if ($this->filterRekapEndDate) {
            $query->whereDate('tanggal_selesai', '<=', $this->filterRekapEndDate);
        }
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
        $pegawai = Auth::user()?->pegawai;

        if (!$pegawai) {
            return;
        }

        //Load Data Cuti Milik Pegawai
        $query = Cuti::where('pegawai_id', $pegawai->id)
            ->with(['jenisCuti', 'approvals.approver.pegawai', 'approvals.approver.role'])
            ->orderBy('created_at', 'desc');

        if ($this->filterRiwayatDate) {
            $query->whereDate('tanggal_mulai', $this->filterRiwayatDate);
        }

        if ($this->filterRiwayatStatus) {
            $query->where('status', $this->filterRiwayatStatus);
        }

        $data = collect($query->get());

        $filename = 'Riwayat_Cuti_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($data) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $activeSheet = $sheet->getActiveSheet();
            $headers = ['Tanggal Pengajuan', 'Tanggal Mulai', 'Tanggal Selesai', 'Jenis Cuti', 'Jumlah', 'Sisa Saldo', 'Status', 'Keterangan', 'Disetujui Oleh'];

            foreach ($headers as $index => $header) {
                $activeSheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            $activeSheet->getStyle('A1:I1')->applyFromArray($this->headerStyle());

            $row = 2;
            foreach ($data as $cuti) {
                $activeSheet->setCellValue('A' . $row, $cuti->tanggal_pengajuan);
                $activeSheet->setCellValue('B' . $row, $cuti->tanggal_mulai);
                $activeSheet->setCellValue('C' . $row, $cuti->tanggal_selesai);
                $activeSheet->setCellValue('D' . $row, $cuti->jenisCuti->nama ?? '-');
                $activeSheet->setCellValue('E' . $row, $cuti->jenisCuti?->dihitung_per_jam ? ($cuti->jumlah_jam . ' jam') : ($cuti->jumlah_hari_cuti . ' hari'));
                $activeSheet->setCellValue('F' . $row, $cuti->saldo_cuti_sesudah ?? '-');
                $activeSheet->setCellValue('G' . $row, $this->statusLabel($cuti->status));
                $activeSheet->setCellValue('H' . $row, $cuti->keterangan);
                $activeSheet->setCellValue('I' . $row, $this->getApproverLabel($cuti));
                $row++;
            }

            foreach (range('A', 'I') as $column) {
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
            $headers = ['Tanggal Mulai', 'Tanggal Selesai', 'Jenis Cuti', 'Jumlah', 'Status', 'Keterangan'];

            foreach ($headers as $index => $header) {
                $activeSheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            $activeSheet->getStyle('A1:F1')->applyFromArray($this->headerStyle());

            $row = 2;
            foreach ($data as $cuti) {
                $activeSheet->setCellValue('A' . $row, $cuti->tanggal_mulai);
                $activeSheet->setCellValue('B' . $row, $cuti->tanggal_selesai);
                $activeSheet->setCellValue('C' . $row, $cuti->jenisCuti->nama ?? '-');
                $activeSheet->setCellValue('D' . $row, $cuti->jenisCuti?->dihitung_per_jam ? ($cuti->jumlah_jam . ' jam') : ($cuti->jumlah_hari_cuti . ' hari'));
                $activeSheet->setCellValue('E' . $row, $this->statusLabel($cuti->status));
                $activeSheet->setCellValue('F' . $row, $cuti->keterangan);
                $row++;
            }

            foreach (range('A', 'F') as $column) {
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

        $pegawai = Auth::user()?->pegawai;

        if (!$pegawai) {
            return;
        }
    //=============================================================================
        // 1. Hitung Masa Kerja
        $masaKerjaBulan = $pegawai?->tanggal_bergabung
            ? Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;
            
        $serviceYear = floor($masaKerjaBulan / 12) + 1;
        
        // 2. Cek apakah pegawai berada di periode Cuti Besar (Tahun ke-7 dan ke-8)
        $isCutiBesar = in_array($serviceYear, [7, 8]);

        if ($isCutiBesar) {
            $this->labelSaldoCuti = 'Sisa Saldo Cuti Besar';
            
            // Hitung sisa cuti besar (Maksimal 66 hari)
            $totalUsedCutiBesar = Cuti::where('pegawai_id', $pegawai->id)
                ->where('jenis_cuti_id', 2) // ID 2 = Cuti Besar
                ->where('status', 'disetujui')
                ->sum('jumlah_hari_cuti');
                
            $this->sisaSaldoCuti = max(0, 66 - $totalUsedCutiBesar);
            $this->cutiTerpakai = $totalUsedCutiBesar;
            $this->cutiTerpakaiBesar = $totalUsedCutiBesar;

        } else {
            $this->labelSaldoCuti = 'Sisa Saldo Cuti Tahunan';
            
            // Ambil dari tabel SaldoCuti
            $saldo = SaldoCuti::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'tahun' => now()->year],
                ['hak_cuti' => 12, 'cuti_terpakai' => 0, 'sisa_cuti' => 12]
            );
            $this->sisaSaldoCuti = $saldo->sisa_cuti;
            $this->cutiTerpakai = $saldo->cuti_terpakai;
            $this->cutiTerpakaiTahunan = $saldo->cuti_terpakai;
        }
    //=============================================================================

    //=============================================================================
        //Load Data Cuti Milik Pegawai  
        $query = Cuti::where('pegawai_id', $pegawai->id)
            ->with(['jenisCuti', 'approvals.approver.pegawai', 'approvals.approver.role'])
            ->orderBy('created_at', 'desc');

        if ($this->filterRiwayatDate) {
            $query->whereDate('tanggal_mulai', $this->filterRiwayatDate);
        }

        if ($this->filterRiwayatStatus) {
            $query->where('status', $this->filterRiwayatStatus);
        }
    //=============================================================================

    //=============================================================================
        //Load Rekapitulasi Milik Pegawai
        $rekapQuery = Cuti::where('pegawai_id', $pegawai->id)
            ->with('jenisCuti')
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->orderBy('tanggal_mulai', 'desc');

        $this->applyRekapPeriodFilter($rekapQuery);
        $this->rekapList = $rekapQuery->get();
    //=============================================================================

        $cutiMelahirkanUsed = collect($this->rekapList)->where('jenis_cuti_id', 3)->sum('jumlah_hari_cuti');

        $this->rekapSummary = [
            'sisa_saldo' => $this->sisaSaldoCuti,
            'label_saldo_cuti' => $this->labelSaldoCuti,
            'cuti_terpakai' => $this->cutiTerpakai,
            'cuti_terpakai_tahunan' => $this->cutiTerpakaiTahunan,
            'cuti_terpakai_besar' => $this->cutiTerpakaiBesar,
            'cuti_terpakai_melahirkan' => $cutiMelahirkanUsed,
            'persentase_tahunan' => $this->rekapSummary['cuti_terpakai_tahunan'] > 0 ? min(100, ($this->rekapSummary['cuti_terpakai_tahunan'] / 12) * 100) : 0,
            'persentase_besar' => $this->rekapSummary['cuti_terpakai_besar'] > 0 ? min(100, ($this->rekapSummary['cuti_terpakai_besar'] / 66) * 100) : 0,
            'persentase_melahirkan' => $this->rekapSummary['cuti_terpakai_melahirkan'] > 0 ? min(100, ($this->rekapSummary['cuti_terpakai_melahirkan'] / 90) * 100) : 0,
        ];

        return view('livewire.dashboard.cuti.index', [
            'cutiList' => $query->paginate(10),
        ]);
    }
}
