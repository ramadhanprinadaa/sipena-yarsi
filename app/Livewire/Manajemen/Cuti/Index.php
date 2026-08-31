<?php

namespace App\Livewire\Manajemen\Cuti;

use App\Models\Cuti;
use App\Models\CutiApproval;
use App\Models\SaldoCuti;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithPagination;

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
        $user = Auth::user();
        $role = $user->role->name ?? '';
        $unitKerjaIds = $this->scopedUnitKerjaIds();
        $userUnitKerjaName = $user->pegawai?->unit_kerja?->name ?? '';

        if ($role === 'Pimpinan') {
            $query->whereHas('pegawai', function ($pegawaiQuery) use ($unitKerjaIds, $userUnitKerjaName) {
                $pegawaiQuery->where(function($q) use ($unitKerjaIds, $userUnitKerjaName) {
                    // Akses default melihat pegawai dalam unit kerjanya
                    if (is_array($unitKerjaIds)) {
                        $q->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
                    }

                    // Akses tambahan: Pimpinan Sekretariat Universitas dapat melihat cuti SDM Universitas
                    if ($userUnitKerjaName === 'Sekretariat Universitas') {
                        $q->orWhereHas('user.role', function ($r) {
                            $r->where('name', 'SDM Universitas');
                        });
                    }

                    // Akses tambahan: Pimpinan Sekretariat Yayasan dapat melihat cuti SDM Yayasan
                    if ($userUnitKerjaName === 'Sekretariat Yayasan') {
                        $q->orWhereHas('user.role', function ($r) {
                            $r->where('name', 'SDM Yayasan');
                        });
                    }
                });
            });
        } elseif (is_array($unitKerjaIds)) {
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
        $this->resetErrorBag('approval');
        $this->confirmAction = $action;
        $this->confirmId = $id;
        $this->confirmTitle = $action === 'approve' ? 'Konfirmasi Setujui' : 'Konfirmasi Tolak';
        $this->confirmMessage = 'Apakah Anda yakin ingin ' . ($action === 'approve' ? 'menyetujui' : 'menolak') . ' pengajuan cuti ini?';
    }

    public function closeApprovalConfirmation(): void
    {
        $this->resetErrorBag('approval');
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

        if ($status === 'disetujui' && !$this->hasEnoughSaldoForApproval($cuti)) {
            $this->addError('approval', 'Sisa saldo cuti tidak mencukupi untuk menyetujui pengajuan ini.');
            return;
        }

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
        $harusPotongSaldo = $cuti->jenisCuti?->memotong_saldo
            || ((int) $cuti->jenis_cuti_id === 4 && $cuti->metode_potongan === 'potong_cuti')
            || (int) $cuti->jenis_cuti_id === 2;

        if (!$harusPotongSaldo || !$cuti->jumlah_hari_cuti) {
            return;
        }

        if ($this->usesCutiBesarBalance($cuti)) {
            $saldoSebelum = $this->remainingCutiBesar($cuti);

            $cuti->update([
                'saldo_cuti_sebelum' => $saldoSebelum,
                'saldo_cuti_sesudah' => max(0, $saldoSebelum - $cuti->jumlah_hari_cuti),
            ]);

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

    private function hasEnoughSaldoForApproval(Cuti $cuti): bool
    {
        $harusPotongSaldo = $cuti->jenisCuti?->memotong_saldo
            || ((int) $cuti->jenis_cuti_id === 4 && $cuti->metode_potongan === 'potong_cuti')
            || (int) $cuti->jenis_cuti_id === 2;

        if (!$harusPotongSaldo || !$cuti->jumlah_hari_cuti) {
            return true;
        }

        if ($this->usesCutiBesarBalance($cuti)) {
            return $this->remainingCutiBesar($cuti) >= $cuti->jumlah_hari_cuti;
        }

        $saldo = SaldoCuti::firstOrCreate(
            ['pegawai_id' => $cuti->pegawai_id, 'tahun' => now()->year],
            ['hak_cuti' => 12, 'cuti_terpakai' => 0, 'sisa_cuti' => 12]
        );

        return $saldo->sisa_cuti >= $cuti->jumlah_hari_cuti;
    }

    private function usesCutiBesarBalance(Cuti $cuti): bool
    {
        if ((int) $cuti->jenis_cuti_id === 2) {
            return true;
        }

        if ((int) $cuti->jenis_cuti_id !== 4 || $cuti->metode_potongan !== 'potong_cuti') {
            return false;
        }

        $masaKerjaBulan = $cuti->pegawai?->tanggal_bergabung
            ? Carbon::parse($cuti->pegawai->tanggal_bergabung)->diffInMonths(Carbon::today())
            : 0;

        return in_array(floor($masaKerjaBulan / 12) + 1, [7, 8]);
    }

    private function remainingCutiBesar(Cuti $cuti): int
    {
        $used = Cuti::where('pegawai_id', $cuti->pegawai_id)
            ->where('id', '!=', $cuti->id)
            ->where(function ($query) {
                $query->where('jenis_cuti_id', 2)
                    ->orWhere(function ($q) {
                        $q->where('jenis_cuti_id', 4)->where('metode_potongan', 'potong_cuti');
                    });
            })
            ->where('status', 'disetujui')
            ->sum('jumlah_hari_cuti');

        return max(0, 66 - $used);
    }

    public function canApprove(Cuti $cuti): bool
    {
        $role = Auth::user()->role->name ?? '';
        $requesterRole = $cuti->pegawai?->user?->role?->name;
        $unitSdmId = (int) $cuti->pegawai?->unit_kerja?->unit_sdm_id;
        $requesterUnitKerjaName = $cuti->pegawai?->unit_kerja?->name ?? '';
        
        $userUnitKerjaName = Auth::user()->pegawai?->unit_kerja?->name ?? '';
        $userUnitKerjaId = Auth::user()->pegawai?->unit_kerja_id;

        // Kumpulan status mentah yang mungkin tercipta dari form pembuatan cuti.
        $pendingStatuses = ['Menunggu Verifikasi Pimpinan', 'Menunggu Verifikasi Atasan', 'Menunggu Verifikasi SDM Universitas', 'Menunggu Verifikasi SDM Yayasan', 'Menunggu Verifikasi Rektor'];

        if ($role === 'Pimpinan') {
            // Aturan Tambahan 1: Pimpinan Sekretariat Universitas menyetujui cuti SDM Universitas
            if ($userUnitKerjaName === 'Sekretariat Universitas' && $requesterRole === 'SDM Universitas') {
                return in_array($cuti->status, $pendingStatuses);
            }

            // Aturan Tambahan 3: Pimpinan Sekretariat Yayasan menyetujui cuti SDM Yayasan
            if ($userUnitKerjaName === 'Sekretariat Yayasan' && $requesterRole === 'SDM Yayasan') {
                return in_array($cuti->status, $pendingStatuses);
            }

            // Aturan Default Pimpinan ke pegawainya
            return $cuti->status === 'Menunggu Verifikasi Pimpinan'
                && $cuti->pegawai?->unit_kerja_id === $userUnitKerjaId
                && !in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan']);
        }

        if ($role === 'Rektor') {
            // Rektor dilarang menyetujui Pimpinan Sekretariat Universitas (dialihkan ke SDM Yayasan)
            return $cuti->status === 'Menunggu Verifikasi Rektor'
                && $requesterRole === 'Pimpinan'
                && $unitSdmId === 2
                && $requesterUnitKerjaName !== 'Sekretariat Universitas'; 
        }

        if ($role === 'SDM Universitas') {
            return $cuti->status === 'Menunggu Verifikasi SDM Universitas'
                && $requesterRole === 'Rektor';
        }

        if ($role === 'SDM Yayasan') {
            // Aturan Tambahan 2: SDM Yayasan menyetujui cuti Pimpinan Sekretariat Universitas
            if ($requesterRole === 'Pimpinan' && $requesterUnitKerjaName === 'Sekretariat Universitas') {
                return in_array($cuti->status, $pendingStatuses);
            }

            // Aturan Default SDM Yayasan
            // Dikecualikan SDM Universitas & SDM Yayasan agar tidak overlap dengan Atasan Pimpinan Sek.
            return $cuti->status === 'Menunggu Verifikasi SDM Yayasan'
                && $unitSdmId === 1
                && !in_array($requesterRole, ['SDM Yayasan', 'SDM Universitas']);
        }

        if ($role === 'Admin') {
            return in_array($cuti->status, $pendingStatuses);
        }

        return false;
    }

    private function serviceYearForDate(Carbon $referenceDate, Carbon $joinDate): int
    {
        return $joinDate->diffInMonths($referenceDate) >= 0
            ? (int) floor($joinDate->diffInMonths($referenceDate) / 12) + 1
            : 1;
    }

    public function displaySaldoCutiSesudah(Cuti $cuti): string
    {
        // Jika jenis cuti bukan cuti besar dan izin sakit, atau tanggal mulai tidak ada, tampilkan saldo_cuti_sesudah dari database
        if ($cuti->jenis_cuti_id !== 2 && $cuti->jenis_cuti_id !== 4 || !$cuti->tanggal_mulai) {
            return $cuti->saldo_cuti_sesudah !== null ? (string) $cuti->saldo_cuti_sesudah : '-';
        }

        $pegawai = $cuti->pegawai;

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
            'Menunggu Verifikasi Pimpinan' => 'Menunggu Persetujuan Pimpinan',
            'Menunggu Verifikasi Rektor' => 'Menunggu Persetujuan Rektor',
            'Menunggu Verifikasi SDM Universitas' => 'Menunggu Persetujuan SDM Universitas',
            'Menunggu Verifikasi SDM Yayasan' => 'Menunggu Persetujuan SDM Yayasan',
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

        $data = $cutiQuery->get();
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

        $rekapQuery = Cuti::with(['pegawai.unit_kerja', 'jenisCuti'])
            ->where('status', 'disetujui');
        $this->applyCutiScope($rekapQuery);
        $this->applyRekapPeriodFilter($rekapQuery);

        if ($this->filterRekapStartDate) {
            $rekapQuery->whereDate('tanggal_mulai', '>=', $this->filterRekapStartDate);
        }

        if ($this->filterRekapEndDate) {
            $rekapQuery->whereDate('tanggal_selesai', '<=', $this->filterRekapEndDate);
        }

        $data = $rekapQuery->get()
            ->groupBy('pegawai_id')
            ->map(function ($items) {
                $first = $items->first();
                
                return [
                    'nama' => $first->pegawai->nama ?? '-',
                    'nip' => $first->pegawai->nip ?? '-',
                    'jumlah_cuti' => $items->sum('jumlah_hari_cuti'),
                    'jumlah_jam' => $items->sum('jumlah_jam'),
                    'sisa_saldo' => $this->displaySaldoCutiSesudah($first) ?? '-',
                ];
            })
            ->values();

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
        //Load Data Cuti Pegawai
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

        //Load Data Rekapitulasi Pegawai
        $rekapQuery = Cuti::with(['pegawai.unit_kerja', 'jenisCuti'])
            ->where('status', 'disetujui');

        $this->applyCutiScope($rekapQuery);
        $this->applyRekapPeriodFilter($rekapQuery);

        $rekapItems = $rekapQuery->get()
            ->groupBy('pegawai_id')
            ->map(function ($items) {
                $first = $items->first();

                return (object) [
                    'pegawai' => $first->pegawai,
                    'jumlah_cuti' => $items->sum('jumlah_hari_cuti'),
                    'jumlah_jam' => $items->sum('jumlah_jam'),
                    'sisa_saldo' => $this->displaySaldoCutiSesudah($first),
                ];
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $rekapItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rekapList = new LengthAwarePaginator(
            $currentItems,
            $rekapItems->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('livewire.manajemen.cuti.index', [
            'cutiList' => $cutiQuery->orderBy('created_at', 'desc')->paginate(10),
            'rekapList' => $rekapList,
            'allowedTabs' => $this->allowedTabs,
        ]);
    }
}