<?php

namespace App\Livewire\Resources;

use App\Models\SumberDaya;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class TabelSumberDaya extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    #[Session]
    public ?string $selectedEkstensiFile = null;

    #[Session]
    public ?string $search = '';

    #[Session]
    public ?string $sortField = null;

    #[Session]
    public ?string $sortDirection = 'asc';

    // Pemetaan ekstensi dropdown
    public array $ekstensiOptions = [
        'pdf'      => 'PDF',
        'doc,docx' => 'Word',
        'xls,xlsx' => 'Excel',
        'ppt,pptx' => 'PowerPoint'
    ];

    #[On('refresh-table')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedEkstensiFile()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function sortIcon(string $field): string
    {
        if ($this->sortField !== $field) {
            return 'fa-sort-down';
        }
        return $this->sortDirection === 'asc' ? 'fa-sort-down' : 'fa-sort-up';
    }

    public function downloadFile(int $id)
    {
        $sumberDaya = SumberDaya::findOrFail($id);

        if (! Storage::disk('public')->exists($sumberDaya->file_path)) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'File tidak ditemukan di server.'
            );
            return;
        }

        $downloadName = str($sumberDaya->judul)
            ->replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '')
            ->append('.' . $sumberDaya->extension);

        return response()->download(
            storage_path('app/public/' . $sumberDaya->file_path),
            $downloadName
        );
    }

    public function editSumberDaya(int $id)
    {
        $this->dispatch('load-edit-sumber-daya', sumber_daya_id: $id);
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if ($this->search) {
            return "Tidak ditemukan data sumber daya dengan kata kunci '{$this->search}'.";
        }

        if ($this->selectedEkstensiFile) {
            return "Belum ada data sumber daya berdasarkan filter yang Anda terapkan.";
        }

        return "Belum ada data sumber daya.";
    }

    #[Computed]
    public function sumberDaya()
    {
        $query = SumberDaya::with('uploader');

        // Pencarian (Search)
        if ($this->search) {
            $query->where('judul', 'like', '%' . $this->search . '%');
        }

        // Filter Ekstensi
        if ($this->selectedEkstensiFile) {
            $extensions = explode(',', $this->selectedEkstensiFile);
            $query->whereIn('extension', $extensions);
        }

        // Pengurutan (Sorting)
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            // Default berurutan sesuai abjad
            $query->orderBy('judul', 'asc');
        }

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.resources.tabel-sumber-daya');
    }
}