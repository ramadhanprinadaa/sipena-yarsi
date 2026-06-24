<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Models\ImportPresensi;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailImportPresensi extends Component
{
    public ?ImportPresensi $file = null;
    public array $errorSummary = [];

    #[On('load-detail-import')]
    public function load(int $fileId): void
    {
        $this->file = ImportPresensi::query()
            ->with([
                'user:id,username',
                'user.pegawai:id,user_id,nama',
            ])
            ->select([
                'id',
                'file_name',
                'periode_mulai',
                'periode_selesai',
                'total_rows',
                'total_created',
                'total_updated',
                'total_skipped',
                'total_failed',
                'error_summary',
                'imported_by',
                'created_at',
            ])
            ->findOrFail($fileId);

        $this->errorSummary = $this->file->error_summary ?? [];
        $this->dispatch('open-detail-import');
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.detail-import-presensi');
    }
}