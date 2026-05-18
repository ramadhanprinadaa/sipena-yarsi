<?php

namespace App\Livewire\Manajemen\Lembur;

use Livewire\Component;
use App\Models\SuratPerintahLembur;
use App\Models\Lembur;
use App\Models\PersetujuanLembur;
use App\Models\PersetujuanLaporan;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $spls = [];
    public $lemburList = [];
    public $laporanList = [];
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
            $this->allowedTabs = ['riwayat', 'rekap', 'verifikasi'];
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

            if ($this->filterSplDate) {
                $splQuery->whereDate('tanggal_lembur', $this->filterSplDate);
            }

            $this->spls = $splQuery->orderBy('created_at', 'desc')->get();
        }

        // Load Riwayat Pengajuan Lembur para pegawai
        if (in_array('riwayat', $this->allowedTabs)) {
            $lemburQuery = Lembur::with(['pegawai', 'suratPerintahLembur', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'persetujuan.approver.pegawai', 'persetujuan.approver.role']);

            // Apply date filter
            if ($this->filterRiwayatDate) {
                $lemburQuery->whereDate('created_at', $this->filterRiwayatDate);
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
                ->with(['pegawai', 'laporanHasilLembur.persetujuan.approver.pegawai', 'laporanHasilLembur.persetujuan.approver.role', 'suratPerintahLembur', 'persetujuan.approver.pegawai', 'persetujuan.approver.role']);

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
        $lembur = Lembur::findOrFail($lemburId);

        if ($lembur->status !== 'Menunggu Verifikasi Atasan' || $lembur->laporanHasilLembur) {
            return;
        }

        $lembur->update([
            'status' => $action === 'approve' ? 'Menunggu Pelaksanaan' : 'Ditolak',
        ]);

        $this->recordApproval($lembur->id, $action, $action === 'approve' ? 'Pengajuan Lembur Disetujui' : 'Pengajuan Lembur Ditolak');
    }

    private function processLaporanApproval(int $lemburId, string $action): void
    {
        $lembur = Lembur::with('laporanHasilLembur')->findOrFail($lemburId);

        if (!$lembur->laporanHasilLembur || $lembur->status !== 'Menunggu Verifikasi Atasan') {
            return;
        }

        $lembur->update([
            'status' => $action === 'approve' ? 'Selesai' : 'Ditolak',
        ]);

        $this->recordLaporanApproval($lembur->laporanHasilLembur->id, $action, $action === 'approve' ? 'Disetujui' : 'Ditolak');
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
}
