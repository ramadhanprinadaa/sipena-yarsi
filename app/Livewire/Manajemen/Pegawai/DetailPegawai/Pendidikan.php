<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai;

use App\Models\JenjangPendidikan;
use App\Models\RiwayatPendidikan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Pendidikan extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public array $jenjangPendidikan;
    public int $pegawai_id;
    public string $nama_pegawai;

    #[Session]
    public ?string $selectedJenjangPendidikan = null;

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

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->jenjangPendidikan = JenjangPendidikan::pluck('kode')->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'selectedJenjangPendidikan',
        ])) {
            $this->resetPage();
        }
    }

    public function baseQuery()
    {
        $query = RiwayatPendidikan::query()
            ->join('jenjang_pendidikan', 'riwayat_pendidikan.jenjang_pendidikan_id', '=', 'jenjang_pendidikan.id')
            ->select([
                'riwayat_pendidikan.id',
                'riwayat_pendidikan.pegawai_id',
                'riwayat_pendidikan.jenjang_pendidikan_id',
                'riwayat_pendidikan.tahun_masuk',
                'riwayat_pendidikan.tahun_lulus',
                'riwayat_pendidikan.file_ijazah',
                'riwayat_pendidikan.file_path',
                'riwayat_pendidikan.updated_by',
            ])
            ->where('riwayat_pendidikan.pegawai_id', $this->pegawai_id)
            ->when($this->selectedJenjangPendidikan, function ($query) {
                $query->where('jenjang_pendidikan.kode', $this->selectedJenjangPendidikan);
            })
            ->with(['jenjangPendidikan', 'editor.pegawai']);

        return $query;
    }

    #[Computed]
    public function pendidikan()
    {
        $query = $this->baseQuery();

        if ($this->sortField) {
            if ($this->sortField === 'urutan') {
                $query->orderBy('jenjang_pendidikan.urutan', $this->sortDirection);
            } else {
                $query->orderBy('riwayat_pendidikan.' . $this->sortField, $this->sortDirection);
            }
        } else {
            $query->orderBy('jenjang_pendidikan.urutan', 'asc');
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if (!empty($this->selectedJenjangPendidikan)) {
            return "Data riwayat pendidikan tidak ditemukan untuk filter yang dipilih.";
        }

        return "Belum ada data riwayat pendidikan pegawai yang terdata.";
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
        return view('livewire.manajemen.pegawai.detail-pegawai.pendidikan');
    }
}
