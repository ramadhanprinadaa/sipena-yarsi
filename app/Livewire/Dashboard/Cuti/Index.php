<?php

namespace App\Livewire\Dashboard\Cuti;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $rekapList = [];
    public $labelSaldoCuti = '';
    public $sisaSaldoCuti = 0;
    public $cutiTerpakai = 0;
    public $cutiTerpakaiTahunan = 0;
    public $cutiTerpakaiBesar = 0;
    public $cutiBesarQuota = 12;
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

    private function serviceYearForDate(Carbon $referenceDate, Carbon $joinDate): int
    {
        return $joinDate->diffInMonths($referenceDate) >= 0
            ? (int) floor($joinDate->diffInMonths($referenceDate) / 12) + 1
            : 1;
    }

    private function currentServiceYear(Pegawai $pegawai): int
    {
        if (!$pegawai->tanggal_bergabung) {
            return 1;
        }

        return (int) floor(Carbon::parse($pegawai->tanggal_bergabung)->diffInMonths(Carbon::today()) / 12) + 1;
    }

    private function currentCutiBesarQuota(Pegawai $pegawai): int
    {
        return in_array($this->currentServiceYear($pegawai), [7, 8]) ? 33 : 12;
    }

    private function currentCutiBesarPeriodRange(Pegawai $pegawai): ?array
    {
        if (!$pegawai->tanggal_bergabung) {
            return null;
        }

        $serviceYear = $this->currentServiceYear($pegawai);

        if (!in_array($serviceYear, [7, 8])) {
            return null;
        }

        $joinDate = Carbon::parse($pegawai->tanggal_bergabung);
        $periodStart = $joinDate->copy()->addYears($serviceYear - 1)->startOfDay();
        $periodEnd = $joinDate->copy()->addYears($serviceYear)->subDay()->endOfDay();

        return [$periodStart, $periodEnd];
    }

    private function usedCutiBesarInCurrentPeriod(Pegawai $pegawai): int
    {
        $period = $this->currentCutiBesarPeriodRange($pegawai);

        if (!$period) {
            return 0;
        }

        [$periodStart, $periodEnd] = $period;

        // Hitung jumlah cuti besar yang telah digunakan (termasuk izin sakit yang memotong saldo) dalam periode saat ini
        return (int) Cuti::where('pegawai_id', $pegawai->id)
            ->where(function ($query) {
                $query->where('jenis_cuti_id', 2)
                    ->orWhere(function ($query) {
                        $query->where('jenis_cuti_id', 4)
                            ->where('metode_potongan', 'potong_cuti');  
                    });
            })
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '>=', $periodStart->toDateString())
            ->whereDate('tanggal_mulai', '<=', $periodEnd->toDateString())
            ->sum('jumlah_hari_cuti');
    }

    public function displaySaldoCutiSesudah(Cuti $cuti): string
    {
        // Jika jenis cuti bukan cuti besar dan izin sakit, atau tanggal mulai tidak ada, tampilkan saldo_cuti_sesudah dari database
        if ($cuti->jenis_cuti_id !== 2 && $cuti->jenis_cuti_id !== 4 || !$cuti->tanggal_mulai) {
            return $cuti->saldo_cuti_sesudah !== null ? (string) $cuti->saldo_cuti_sesudah : '-';
        }

        $pegawai = Auth::user()?->pegawai;

        // Jika pegawai tidak ditemukan atau tanggal bergabung tidak ada, tampilkan saldo_cuti_sesudah dari database
        if (!$pegawai || !$pegawai->tanggal_bergabung) {
            return $cuti->saldo_cuti_sesudah !== null ? (string) $cuti->saldo_cuti_sesudah : '-';
        }

        $serviceYear = $this->serviceYearForDate(Carbon::parse($cuti->tanggal_mulai), Carbon::parse($pegawai->tanggal_bergabung));

        // Jika pegawai tidak berada di periode cuti besar (tahun ke-7 atau ke-8), tampilkan saldo_cuti_sesudah dari database
        if (!in_array($serviceYear, [7, 8])) {
            return $cuti->saldo_cuti_sesudah !== null ? (string) $cuti->saldo_cuti_sesudah : '-';
        }

        $joinDate = Carbon::parse($pegawai->tanggal_bergabung);
        $periodStart = $joinDate->copy()->addYears($serviceYear - 1)->startOfDay();
        $usedBeforeThisCuti = (int) Cuti::where('pegawai_id', $pegawai->id)
            ->where(function ($query) {
                $query->where('jenis_cuti_id', 2)
                    ->orWhere(function ($query) {
                        $query->where('jenis_cuti_id', 4)
                            ->where('metode_potongan', 'potong_cuti');
                    });
            })
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '>=', $periodStart->toDateString())
            ->whereDate('tanggal_mulai', '<=', Carbon::parse($cuti->tanggal_mulai)->toDateString())
            ->sum('jumlah_hari_cuti');

        $remaining = max(0, 33 - $usedBeforeThisCuti);

        return (string) $remaining;
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

    public function cutiTahunanProgress(): float
    {
        return $this->rekapSummary['cuti_terpakai_tahunan'] > 0
            ? min(100, ($this->rekapSummary['cuti_terpakai_tahunan'] / 12) * 100)
            : 0;
    }

    public function cutiBesarProgress(): float
    {
        return $this->rekapSummary['cuti_terpakai_besar'] > 0
            ? min(100, ($this->rekapSummary['cuti_terpakai_besar'] / ($this->cutiBesarQuota ?: 1)) * 100)
            : 0;
    }

    public function cutiMelahirkanProgress(): float
    {
        return $this->rekapSummary['cuti_terpakai_melahirkan'] > 0
            ? min(100, ($this->rekapSummary['cuti_terpakai_melahirkan'] / 90) * 100)
            : 0;
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
            return view('livewire.dashboard.cuti.index', [
                'cutiList' => new LengthAwarePaginator([], 0, 10),
            ]);
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
            $this->cutiBesarQuota = $this->currentCutiBesarQuota($pegawai);

            $totalUsedCutiBesar = $this->usedCutiBesarInCurrentPeriod($pegawai);

            $this->sisaSaldoCuti = max(0, $this->currentCutiBesarQuota($pegawai) - $totalUsedCutiBesar);
            $this->cutiTerpakai = $totalUsedCutiBesar;
            $this->cutiTerpakaiBesar = $totalUsedCutiBesar;

        } else {
            $this->labelSaldoCuti = 'Sisa Saldo Cuti Tahunan';
            $this->cutiBesarQuota = 12;

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
            ->where('status', 'disetujui')
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