<?php

namespace App\Livewire\Config;

use Livewire\Component;
use Livewire\WithPagination;

class UnitKerja extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'nama';
    public string $sortDirection = 'asc';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $kode = '';
    public string $nama = '';
    public string $singkatan = '';
    public string $kepala = '';
    public string $status = 'aktif';

    protected $rules = [
        'kode'      => 'required|string|max:10',
        'nama'      => 'required|string|max:100',
        'singkatan' => 'required|string|max:20',
        'kepala'    => 'nullable|string|max:100',
        'status'    => 'required|in:aktif,nonaktif',
    ];

    protected $messages = [
        'kode.required'      => 'Kode unit kerja wajib diisi.',
        'nama.required'      => 'Nama unit kerja wajib diisi.',
        'singkatan.required' => 'Singkatan wajib diisi.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        // $unitKerja = UnitKerja::findOrFail($id);
        // $this->fill($unitKerja->only(['kode', 'nama', 'singkatan', 'kepala', 'status']));
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        // if ($this->editingId) {
        //     UnitKerja::findOrFail($this->editingId)->update([...]);
        //     $this->dispatch('notify', type: 'success', message: 'Unit kerja berhasil diperbarui.');
        // } else {
        //     UnitKerja::create([...]);
        //     $this->dispatch('notify', type: 'success', message: 'Unit kerja berhasil ditambahkan.');
        // }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        // UnitKerja::findOrFail($this->deletingId)->delete();
        // $this->dispatch('notify', type: 'success', message: 'Unit kerja berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->reset(['kode', 'nama', 'singkatan', 'kepala', 'editingId']);
        $this->status = 'aktif';
        $this->resetErrorBag();
    }

    public function render()
    {
        // $unitKerjas = UnitKerja::query()
        //     ->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%")
        //                                       ->orWhere('kode', 'like', "%{$this->search}%"))
        //     ->orderBy($this->sortField, $this->sortDirection)
        //     ->paginate(10);

        return view('livewire.config.unit-kerja', [
            // 'unitKerjas' => $unitKerjas,
        ]);
    }
}