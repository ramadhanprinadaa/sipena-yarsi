<?php

namespace App\Livewire\Config\Kalender;

use App\Models\HariLibur;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailTanggal extends Component
{
    public $date = null;
    public $day = [];
    public $editingHolidayId = null;

    public $form = [
        'nama_hari_libur' => '',
        'jenis_hari_libur' => '',
        'keterangan' => '',
    ];

    public $originalForm = [];

    public array $jenisHariLibur = [
        'Hari Libur Nasional',
        'Hari Libur Cuti Bersama',
        'Hari Libur Institusi',
    ];

    #[On('load-detail-modal')]
    public function loadData($day = null)
    {
        if (! $day) {
            return;
        }
        $this->day = $day;
        $this->date = $day['date'];
        $this->dispatch('open-detail-modal');
    }

    public function startEdit($holidayId)
    {
        $holiday = HariLibur::find($holidayId);
        if (! $holiday) {
            return;
        }
        $this->editingHolidayId = $holiday->id;
        $this->form = [
            'nama_hari_libur' => $holiday->nama_hari_libur,
            'jenis_hari_libur' => $holiday->jenis_hari_libur,
            'keterangan' => $holiday->keterangan,
        ];
        $this->originalForm = $this->form;
        $this->resetValidation();
    }

    public function cancelEdit()
    {
        $this->editingHolidayId = null;
        $this->form = [
            'nama_hari_libur' => '',
            'jenis_hari_libur' => '',
            'keterangan' => '',
        ];
        $this->originalForm = [];
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'form.nama_hari_libur' => ['required', 'string', 'max:100'],
            'form.jenis_hari_libur' => ['required', 'string', 'in:' . implode(',', $this->jenisHariLibur)],
            'form.keterangan' => ['nullable'],
        ];
    }

    protected function messages()
    {
        return [
            'form.nama_hari_libur.required' => 'Nama hari libur wajib diisi.',
            'form.jenis_hari_libur.required' => 'Jenis hari libur wajib dipilih.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.nama_hari_libur' => 'nama hari libur',
            'form.jenis_hari_libur' => 'jenis hari libur',
            'form.keterangan' => 'keterangan',
        ];
    }

    public function getIsDirtyProperty()
    {
        return $this->form != $this->originalForm;
    }

    public function save()
    {
        if (! $this->editingHolidayId) {
            return;
        }
        $validated = $this->validate()['form'];
        $holiday = HariLibur::find($this->editingHolidayId);
        if (! $holiday) {
            return;
        }
        $holiday->update($validated);
        $this->refreshDay();
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Event Tanggal berhasil diperbarui.'
        );
        $this->cancelEdit();
        $this->dispatch('refresh-calendar');
    }

    public function deleteHoliday($holidayId)
    {
        $holiday = HariLibur::find($holidayId);
        if (! $holiday) {
            return;
        }
        $holiday->delete();
        $this->refreshDay();
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Event Tanggal berhasil dihapus.'
        );
        $this->dispatch('refresh-calendar');
    }

    private function refreshDay()
    {
        $holidays = HariLibur::query()
            ->whereDate('tanggal', $this->date)
            ->get();
        $this->day['holidays'] = $holidays;
        $this->day['is_holiday'] = $holidays->isNotEmpty();
    }

    #[On('close-detail-modal')]
    public function close()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.config.kalender.detail-tanggal');
    }
}
