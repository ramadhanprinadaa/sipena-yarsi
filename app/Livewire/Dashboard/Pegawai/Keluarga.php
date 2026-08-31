<?php

namespace App\Livewire\Dashboard\Pegawai;

use App\Models\JenisKeluarga;
use App\Models\Keluarga as KeluargaModel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Keluarga extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public array $hubunganKeluarga;
    public ?int $pegawai_id = null;
    public ?string $nama_pegawai = null;

    // Filter
    #[Session]
    public ?string $selectedHubungan = null;

    // Search
    #[Session]
    public $search = '';

    // Filter Urutan Data
    #[Session]
    public ?string $sortField = null;
    #[Session]
    public ?string $sortDirection = 'asc';

    #[On('refresh-table')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function mount(?int $pegawai_id = null)
    {
        $this->pegawai_id = $pegawai_id;
        $this->hubunganKeluarga = JenisKeluarga::pluck('jenis')->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedHubungan',
        ])) {
            $this->resetPage();
        }
    }

    public function baseQuery()
    {
        $query = KeluargaModel::query()
            ->select([
                'keluarga.id',
                'keluarga.pegawai_id',
                'keluarga.jenis_keluarga_id',
                'keluarga.nama',
                'keluarga.tempat_lahir',
                'keluarga.tanggal_lahir',
                'keluarga.pekerjaan',
                'keluarga.no_telpon'
            ])
            ->where('keluarga.pegawai_id', $this->pegawai_id)
            ->with(['jenisKeluarga:id,jenis']);

        if ($this->pegawai_id === null) {
            return $query->whereRaw('1 = 0');
        }

        // Search by name
        if (!empty($this->search)) {
            $query->where('keluarga.nama', 'like', '%' . $this->search . '%');
        }

        // Filter by Jenis Keluarga
        if (!empty($this->selectedHubungan)) {
            $query->whereHas('jenisKeluarga', function ($q) {
                $q->where('jenis', $this->selectedHubungan);
            });
        }

        return $query;
    }

    #[Computed]
    public function keluarga()
    {
        $query = $this->baseQuery();

        // Logika Sorting
        if ($this->sortField) {
            $query->orderBy('keluarga.' . $this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('keluarga.tanggal_lahir', 'asc');
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if ($this->pegawai_id === null) {
            return "Akun Anda belum tertaut dengan data pegawai manapun.";
        }

        if ($this->search) {
            return "Tidak ditemukan data keluarga dengan kata kunci '{$this->search}'.";
        }
        if (!empty($this->selectedHubungan)) {
            return "Data keluarga tidak ditemukan untuk filter yang dipilih.";
        }

        return "Belum ada data keluarga pegawai yang terdaftar.";
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function sortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'fa-sort-up text-gray-300';
        }
        return $this->sortDirection === 'asc'
            ? 'fa-sort-down'
            : 'fa-sort-up';
    }

    public function render()
    {
        return view('livewire.dashboard.pegawai.keluarga');
    }
}