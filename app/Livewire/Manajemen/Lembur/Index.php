<?php

namespace App\Livewire\Manajemen\Lembur;

use Livewire\Component;
use App\Models\SuratPerintahLembur;
use App\Models\Lembur;
use App\Models\PersetujuanLembur;
use App\Models\PersetujuanLaporan;
use App\Models\UnitKerja;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    public $confirmAction = null;
    public $confirmType = null;
    public $confirmId = null;
    public $confirmTitle = '';
    public $confirmMessage = '';
    public $password = '';
    public $confirmStep = 'confirmation';
    
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
        $user = Auth::user();
        $role = $user->role->name ?? '';
        $unitKerjaName = $user->pegawai?->unit_kerja?->name ?? '';

        if ($role === 'Pimpinan') {
            $query->where(function ($q) use ($unitKerjaIds, $unitKerjaName) {
                // Scope 1: SPL di dalam lingkup Unit Kerjanya
                if (is_array($unitKerjaIds)) {
                    $q->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
                }
                
                // Scope 2: Logika Bisnis Akses Pimpinan ke role SDM
                if ($unitKerjaName === 'Sekretariat Universitas') {
                    $q->orWhereHas('pegawai.user.role', function ($r) {
                        $r->where('name', 'SDM Universitas');
                    });
                }
                
                if ($unitKerjaName === 'Sekretariat Yayasan') {
                    $q->orWhereHas('pegawai.user.role', function ($r) {
                        $r->where('name', 'SDM Yayasan');
                    });
                }
            });
        } elseif (is_array($unitKerjaIds)) {
            $query->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
        }
    }

    private function applyLemburScope($query): void
    {
        $unitKerjaIds = $this->scopedUnitKerjaIds();
        $user = Auth::user();
        $role = $user->role->name ?? '';
        $unitKerjaName = $user->pegawai?->unit_kerja?->name ?? '';

        if ($role === 'Pimpinan') {
            $query->whereHas('pegawai', function ($pegawaiQuery) use ($unitKerjaIds, $unitKerjaName) {
                $pegawaiQuery->where(function($q) use ($unitKerjaIds, $unitKerjaName) {
                    
                    if (is_array($unitKerjaIds)) {
                        $q->whereIn('unit_kerja_id', $unitKerjaIds ?: [0]);
                    }
                    
                    // Logika Bisnis Akses Pimpinan ke data Lembur milik role SDM
                    if ($unitKerjaName === 'Sekretariat Universitas') {
                        $q->orWhereHas('user.role', function ($r) {
                            $r->where('name', 'SDM Universitas');
                        });
                    }
                    
                    if ($unitKerjaName === 'Sekretariat Yayasan') {
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
        $this->password = '';

        $label = $action === 'approve' ? 'Setujui' : 'Tolak';
        $target = $type === 'laporan' ? 'Laporan Lembur' : '';

        $this->confirmTitle = 'Konfirmasi ' . $label;
        $this->confirmMessage = 'Apakah Anda yakin ingin ' . strtolower($label) . ' ' . $target . ' ini?';

        // Aktifkan tahap password jika aksi adalah menyetujui atau menolak laporan
        if ($type === 'laporan' && $action === 'approve' || $action === 'reject') {
            $this->confirmStep = 'password';
        } else {
            $this->confirmStep = 'confirmation';
        }
    }

    public function verifyPassword(): void
    {
        $this->validate([
            'password' => 'required',
        ]);

        if (Hash::check($this->password, Auth::user()->password)) {
            $this->confirmStep = 'confirmation';
            $this->resetErrorBag('password');
        } else {
            $this->addError('password', 'Password yang Anda masukkan salah.');
        }
    }

    public function closeApprovalConfirmation(): void
    {
        $this->reset(['confirmAction', 'confirmType', 'confirmId', 'confirmTitle', 'confirmMessage', 'password', 'confirmStep']);
        $this->resetErrorBag();
    }

    public function confirmApproval(): void
    {
        if (!$this->confirmType || !$this->confirmAction || !$this->confirmId) {
            return;
        }

        if ($this->confirmType === 'laporan') {
            $this->processLaporanApproval($this->confirmId, $this->confirmAction);
        }

        $this->closeApprovalConfirmation();
        $this->loadData();
    }

    private function processLaporanApproval(int $lemburId, string $action): void
    {
        $lembur = Lembur::with(['pegawai.unit_kerja', 'pegawai.user.role', 'laporanHasilLembur.persetujuan'])->findOrFail($lemburId);

        if (!$lembur->laporanHasilLembur || !$this->canApproveLaporan($lembur)) {
            return;
        }

        $this->recordLaporanApproval($lembur->laporanHasilLembur->id, $action, $action === 'approve' ? 'Disetujui' : 'Ditolak');

        if ($action === 'approve' && in_array(Auth::user()->role->name ?? '', ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])) {
            $lembur->update([
                'status' => 'Selesai',
            ]);
        } elseif ($action === 'reject' && in_array(Auth::user()->role->name ?? '', ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])) {
            $lembur->update([
                'status' => 'Ditolak',
            ]);
        }

        $this->dispatch('laporan-updated');
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

    public function canApproveLaporan(Lembur $lembur): bool
    {
        if (!$lembur->laporanHasilLembur) {
            return false;
        }

        $role = Auth::user()->role->name ?? '';
        $approvalStatus = $this->getLaporanApprovalStatus($lembur);
        $requesterRole = $this->requesterRole($lembur);
        $unitKerjaName = $lembur->pegawai?->unit_kerja?->name ?? '';

        if ($role === 'SDM Universitas') {
            return $approvalStatus === 'Menunggu Verifikasi SDM Universitas'
                && (
                    (!in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 2)
                    || ($requesterRole === 'Pimpinan'
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 2 
                        && $unitKerjaName !== 'Sekretariat Universitas') // Mencegah SDM Universitas approve
                );
        }

        if ($role === 'SDM Yayasan') {
            return $approvalStatus === 'Menunggu Verifikasi SDM Yayasan'
                && (
                    (!in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])
                        && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 1)
                    || ($requesterRole === 'Pimpinan'
                        && ((int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 1 || $unitKerjaName === 'Sekretariat Universitas')) // Memberikan hak kepada SDM Yayasan
                    || $requesterRole === 'Rektor'
                );
        }

        if ($role === 'Pimpinan') {
            if ($approvalStatus !== 'Menunggu Verifikasi Atasan') {
                return false;
            }

            $userUnitKerjaName = Auth::user()->pegawai?->unit_kerja?->name ?? '';
            $userUnitKerjaId = Auth::user()->pegawai?->unit_kerja_id;

            if ($lembur->pegawai?->unit_kerja_id === $userUnitKerjaId && !in_array($requesterRole, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])) {
                return true;
            }

            if ($userUnitKerjaName === 'Sekretariat Universitas' && $requesterRole === 'SDM Universitas') {
                return true;
            }

            if ($userUnitKerjaName === 'Sekretariat Yayasan' && $requesterRole === 'SDM Yayasan') {
                return true;
            }

            return false;
        }

        if ($role === 'Rektor') {
            return $approvalStatus === 'Menunggu Verifikasi Rektor'
                && $requesterRole === 'Pimpinan'
                && (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id === 2
                && $unitKerjaName !== 'Sekretariat Universitas'; // Mencegah Rektor approve
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
        $unitKerjaName = $lembur->pegawai?->unit_kerja?->name ?? '';

        if ($role === 'Pimpinan') {
            // Arahkan Pimpinan Sekretariat Universitas ke SDM Yayasan
            return ($unitSdmId === 1 || $unitKerjaName === 'Sekretariat Universitas')
                ? 'Menunggu Verifikasi SDM Yayasan'
                : 'Menunggu Verifikasi Rektor';
        }

        if ($role === 'Rektor') {
            return 'Menunggu Verifikasi SDM Universitas';
        }

        if (in_array($role, ['SDM Universitas', 'SDM Yayasan'])) {
            return 'Menunggu Verifikasi Atasan';
        }

        return 'Menunggu Verifikasi Atasan';
    }

    private function initialLaporanApprovalStatusFor(Lembur $lembur): string
    {
        $role = $this->requesterRole($lembur);
        $unitSdmId = (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id;
        $unitKerjaName = $lembur->pegawai?->unit_kerja?->name ?? '';

        // Jika role Staff atau role lain yang bukan Pimpinan, Rektor, SDM Universitas, atau SDM Yayasan
        if (!in_array($role, ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan'])) {
            return ($unitSdmId === 1)
            ? 'Menunggu Verifikasi Atasan' 
            : 'Menunggu Verifikasi Atasan';
        }
        // Jika role adalah Pimpinan Sekretariat Universitas atau Pimpinan di bawah SDM Yayasan
        if ($role === 'Pimpinan') {
            // Arahkan Pimpinan Sekretariat Universitas ke SDM Yayasan
            return ($unitSdmId === 1 || $unitKerjaName === 'Sekretariat Universitas')
                ? 'Menunggu Verifikasi SDM Yayasan'
                : 'Menunggu Verifikasi Rektor';
        }
        // Jika role adalah Rektor, maka status awal adalah menunggu verifikasi SDM Universitas
        if ($role === 'Rektor') {
            return 'Menunggu Verifikasi SDM Universitas';
        }
        // Jika role adalah SDM Universitas atau SDM Yayasan, maka status awal adalah menunggu verifikasi atasan dari Unit Kerja mereka
        if (in_array($role, ['SDM Universitas', 'SDM Yayasan'])) {
            return 'Menunggu Verifikasi Atasan';
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
    #[On('laporan-updated')]
    public function refreshData()
    {
        $this->loadData();
    }

    public function render()
    {
        // Load SPL untuk Pimpinan
        $splQuery = SuratPerintahLembur::with(['pegawai', 'unitKerja', 'lembur']);

        $this->applySplScope($splQuery);

        if ($this->filterSplDate) {
            $splQuery->whereDate('tanggal_lembur', $this->filterSplDate);
        }

        // Load Riwayat Pengajuan Lembur para pegawai
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

        // Load Laporan Lembur para pegawai
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

        // Load Rekapitulasi Lembur para pegawai
        $rekapQuery = Lembur::with('pegawai')
            ->where('status', 'Selesai')
            ->whereHas('laporanHasilLembur.persetujuan', function ($query) {
                $query->where('status', 'Disetujui');
            });
        $this->applyLemburScope($rekapQuery);
        $this->applyRekapPeriodFilter($rekapQuery);

        return view('livewire.manajemen.lembur.index', [
            'spls' => $splQuery->orderBy('created_at', 'desc')->paginate(10),
            'lemburList' => $lemburQuery->orderBy('created_at', 'desc')->paginate(10),
            'laporanList' => $laporanQuery->orderBy('updated_at', 'desc')->paginate(10),
            'rekapList' => $rekapQuery->select('pegawai_id')
                            ->selectRaw('COUNT(*) as hari')
                            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai))/60 as total_jam')
                            ->with('pegawai')
                            ->groupBy('pegawai_id')
                            ->paginate(10),
            'allowedTabs' => $this->allowedTabs,
        ]);
    }

    public function exportRiwayatExcel()
    {
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

        $data = $lemburQuery->orderBy('created_at', 'desc')->get();
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
        $rekapQuery = Lembur::with('pegawai')
        ->where('status', 'Selesai')
            ->whereHas('laporanHasilLembur.persetujuan', function ($query) {
                $query->where('status', 'Disetujui');
            });
        $this->applyLemburScope($rekapQuery);
        $this->applyRekapPeriodFilter($rekapQuery);

        $data = $rekapQuery->select('pegawai_id')
            ->selectRaw('COUNT(*) as hari')
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai))/60 as total_jam')
            ->with('pegawai')
            ->groupBy('pegawai_id')
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->pegawai->nama ?? '-',
                    'nip' => $item->pegawai->nip ?? '-',
                    'hari' => $item->hari,
                    'total_jam' => $item->total_jam,
                ];
            })->toArray();
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