<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Models\ImportPresensi;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Computed;

class TabelRiwayatImportPresensi extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    #[Session]
    public string $search = '';

    #[Session]
    public ?string $selectedPeriodeMulai = null;

    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    #[On('refresh-table-import')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedPeriodeMulai',
            'selectedPeriodeSelesai',
        ])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function filePresensi()
    {
        $oneYearAgo = now()->subYear();

        $filterMulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat(
                'd/m/Y',
                $this->selectedPeriodeMulai
            )->startOfDay()
            : null;

        $filterSelesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat(
                'd/m/Y',
                $this->selectedPeriodeSelesai
            )->endOfDay()
            : null;

        return ImportPresensi::query()

            ->select([
                'id',
                'file_name',
                'periode_mulai',
                'periode_selesai',
                'imported_by',
                'created_at',
            ])

            ->with([
                'user:id,username',
                'user.pegawai:id,user_id,nama',
            ])

            // Default 1 tahun terakhir
            ->when(
                !$filterMulai && !$filterSelesai,
                fn($query) =>
                $query->where(
                    'created_at', '>=', $oneYearAgo
                )
            )

            // Filter periode overlap
            ->when(
                $filterMulai || $filterSelesai,
                function ($query) use (
                    $filterMulai,
                    $filterSelesai
                ) {
                    $mulai = $filterMulai ?? $filterSelesai;
                    $selesai = $filterSelesai ?? $filterMulai;

                    $query
                        ->whereDate(
                            'periode_mulai',
                            '<=',
                            $selesai
                        )
                        ->whereDate(
                            'periode_selesai',
                            '>=',
                            $mulai
                        );
                }
            )

            // Search
            ->when(
                $this->search,
                function ($query) {
                    $search = '%' . trim($this->search) . '%';
                    $query->where(function ($q) use ($search) {
                        $q->where(
                            'file_name',
                            'like',
                            $search
                        )
                            ->orWhereHas(
                                'user',
                                function ($user) use ($search) {
                                    $user->where(
                                        'username',
                                        'like',
                                        $search
                                    )
                                        ->orWhereHas(
                                            'pegawai',
                                            fn($pegawai) =>
                                            $pegawai->where(
                                                'nama',
                                                'like',
                                                $search
                                            )
                                        );
                                }
                            );
                    });
                }
            )
            ->latest('created_at')
            ->paginate(5);
    }

    public function showDetailFile(int $id): void
    {
        $this->dispatch('load-detail-import', fileId: $id);
    }

    public function render()
    {
        return view(
            'livewire.manajemen.presensi.tabel-riwayat-import-presensi',
        );
    }
}