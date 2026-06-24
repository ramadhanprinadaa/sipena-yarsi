<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Models\Presensi;
use App\Models\PresensiLog;
use App\Services\StatusKehadiranService2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailRiwayatPresensi extends Component
{
    public ?Presensi $presensi = null;

    public bool $isEdit = false;

    public array $originalForm = [];
    public array $form = [
        'jam_masuk'  => '',
        'jam_keluar' => '',
        'keterangan' => '',
    ];

    #[On('load-detail-riwayat')]
    public function load(int $presensiId): void
    {
        $this->presensi = Presensi::query()
            ->with([
                'user:id,username',
                'user.pegawai:id,user_id,nama',
                'statusKehadiran:id,status,kondisi,warna',
                'latestLog',
            ])
            ->findOrFail($presensiId);
        $this->fillForm();
        $this->dispatch('open-detail-riwayat');
    }

    public function fillForm()
    {
        $jamMasuk = $this->presensi?->jam_masuk ? Carbon::parse($this->presensi->jam_masuk)->format('H:i') : null;
        $jamKeluar = $this->presensi?->jam_keluar ? Carbon::parse($this->presensi->jam_keluar)->format('H:i') : null;

        $this->form = [
            'jam_masuk'  => $jamMasuk,
            'jam_keluar' => $jamKeluar,
            'keterangan' => '',
        ];

        $this->originalForm = $this->form;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'jam_masuk',
            'jam_keluar',
            'keterangan',
        ]);
    }

    #[On('close-detail-riwayat')]
    public function close()
    {
        $this->reset();
        $this->isEdit = false;
    }

    #[On('show-edit')]
    public function edit(): void
    {
        $this->isEdit = true;
        $this->fillForm();
    }

    public function cancelEdit(): void
    {
        $this->isEdit = false;
        $this->resetValidation();
        $this->fillForm();
    }

    protected function rules(): array
    {
        return [
            'form.jam_masuk'  => 'nullable|date_format:H:i',
            'form.jam_keluar' => 'nullable|date_format:H:i',
            'form.keterangan' => 'required|string|min:5',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.jam_masuk.date_format'  => 'Jam tidak sesuai format.',
            'form.jam_keluar.date_format' => 'Jam tidak sesuai format.',
            'form.keterangan.required'    => 'Keterangan perubahan wajib diisi.',
            'form.keterangan.min'         => 'Keterangan minimal 5 karakter.',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'form.jam_masuk'  => 'Jam Masuk',
            'form.jam_keluar' => 'Jam Keluar',
            'form.keterangan' => 'Keterangan Perubahan',
        ];
    }

    public function getIsDirtyProperty(): bool
    {
        return
            $this->form['jam_masuk'] !== $this->originalForm['jam_masuk'] || $this->form['jam_keluar'] !== $this->originalForm['jam_keluar'];
    }

    public function updated($property): void
    {
        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    public function save(StatusKehadiranService2 $statusService): void
    {
        if (!$this->isDirty) return;

        $this->validate();

        // Cari tahu detail perubahan data untuk log riwayat
        $changes = [];
        if ($this->form['jam_masuk'] !== $this->originalForm['jam_masuk']) {
            $changes['jam_masuk'] = [
                'old' => $this->originalForm['jam_masuk'],
                'new' => $this->form['jam_masuk']
            ];
        }
        if ($this->form['jam_keluar'] !== $this->originalForm['jam_keluar']) {
            $changes['jam_keluar'] = [
                'old' => $this->originalForm['jam_keluar'],
                'new' => $this->form['jam_keluar']
            ];
        }

        // Resolusi status_kehadiran_id otomatis lewat service
        $newStatusId = $statusService->resolve(
            pegawaiId: $this->presensi->pegawai_id,
            tanggal: Carbon::parse($this->presensi->tanggal)->format('Y-m-d'),
            jamMasuk: $this->form['jam_masuk'],
            jamKeluar: $this->form['jam_keluar'],
        );

        // Update record Presensi Utama
        $this->presensi->update([
            'jam_masuk'           => $this->form['jam_masuk'],
            'jam_keluar'          => $this->form['jam_keluar'],
            'status_kehadiran_id' => $newStatusId,
            'updated_by'          => Auth::id(),
        ]);

        // Simpan log perubahan ke database
        PresensiLog::create([
            'presensi_id'        => $this->presensi->id,
            'import_presensi_id' => $this->presensi->last_import_presensi_id,
            'edited_by'          => Auth::id(),
            'changes'            => $changes,
            'keterangan'         => $this->form['keterangan'],
        ]);

        $this->presensi->refresh();
        $this->resetValidation();
        $this->fillForm();
        $this->isEdit = false;

        $this->dispatch('refresh-table-riwayat-presensi');
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Perubahan presensi berhasil disimpan.',
        ]);
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.detail-riwayat-presensi');
    }
}
