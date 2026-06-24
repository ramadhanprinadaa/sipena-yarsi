<?php

namespace App\Livewire\Dashboard\Presensi;

use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailRiwayatPresensi extends Component
{
    public ?Presensi $presensi = null;

    #[On('load-detail-riwayat')]
    public function load(int $presensiId): void
    {
        $pegawai_id = Auth::user()->pegawai?->id;

        $this->presensi = Presensi::query()
            ->with([
                'statusKehadiran:id,status,kondisi,warna',

                // Biarkan latestLog tanpa filter kolom agar terhindar dari Error 1052 (latestOfMany Ambiguous)
                'latestLog',
                'presensiLogs' => function ($query) {
                    $query->select('id', 'presensi_id', 'edited_by', 'created_at', 'keterangan', 'changes')
                        ->latest('created_at')
                        ->limit(5);
                },
                'presensiLogs.editor:id,username',
                'presensiLogs.editor.pegawai:id,user_id,nama',
            ])
            ->where('pegawai_id', $pegawai_id)
            ->findOrFail($presensiId);

        $this->dispatch('open-detail-riwayat');
    }

    /**
     * Computed Property: Memproses dan memformat data log perubahan (changes)
     */
    #[Computed]
    public function riwayatPerubahan()
    {
        if (!$this->presensi || $this->presensi->presensiLogs->isEmpty()) {
            return collect();
        }

        return $this->presensi->presensiLogs->map(function ($log) {

            // Ekstrak nama editor dari masing-masing baris PresensiLog
            $editorName = $log->editor?->pegawai?->nama
                ?? $log->editor?->username
                ?? 'Administrator (Sistem)';

            $formattedChanges = [];

            // Loop data JSON changes yang sudah di-cast jadi array
            if (is_array($log->changes)) {
                foreach ($log->changes as $key => $change) {
                    $label = match ($key) {
                        'jam_masuk'           => 'Jam Check In',
                        'jam_keluar'          => 'Jam Check Out',
                        'status_kehadiran_id' => 'Status Kehadiran',
                        default               => ucwords(str_replace('_', ' ', $key)),
                    };

                    $formattedChanges[] = [
                        'label' => $label,
                        'old'   => $change['old'] ?? '-',
                        'new'   => $change['new'] ?? '-',
                    ];
                }
            }

            return [
                'id'          => $log->id,
                'waktu'       => \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i'),
                'editor_name' => $editorName, // Nama pengedit akan unik per baris history
                'keterangan'  => $log->keterangan,
                'changes'     => $formattedChanges,
            ];
        });
    }

    public function render()
    {
        return view('livewire.dashboard.presensi.detail-riwayat-presensi');
    }
}