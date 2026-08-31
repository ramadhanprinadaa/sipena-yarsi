<?php

namespace App\Livewire\Config\Kalender;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\HariLibur;
use Carbon\Carbon;


class TambahHariLibur extends Component
{
    public $form = [
        'tanggal'           => null,
        'nama_hari_libur'   => '',
        'jenis_hari_libur'  => '',
        'keterangan'        => '',
    ];
    public $jenisHariLibur = [
        'Hari Libur Nasional',
        'Hari Libur Cuti Bersama',
        'Hari Libur Institusi',
    ];

    #[On('close-add-modal')]
    public function handleClose()
    {
        $this->resetForm();
    }

    protected function rules()
    {
        return [
            'form.tanggal'          => ['required', 'date_format:d/m/Y'],
            'form.nama_hari_libur'  => ['required', 'string', 'max:100'],
            'form.jenis_hari_libur' => ['required'],
            'form.keterangan'       => ['nullable'],
        ];
    }

    protected function messages()
    {
        return [
            'form.tanggal.required' => 'Tanggal wajib diisi.',
            'form.tanggal.date_format' => 'Tanggal tidak valid.',
            'form.nama_hari_libur.required' => 'Nama hari libur wajib diisi.',
            'form.nama_hari_libur.string' => 'Nama hari libur harus berupa teks.',
            'form.nama_hari_libur.max' => 'Nama hari libur maksimal 100 karakter.',
            'form.nama_hari_libur.unique' => 'Nama hari libur sudah digunakan.',
            'form.jenis_hari_libur.required' => 'Jenis hari libur wajib dipilih.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.tanggal' => 'tanggal',
            'form.nama_hari_libur' => 'nama hari libur',
            'form.jenis_hari_libur' => 'jenis hari libur',
            'form.keterangan' => 'keterangan',
        ];
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    public function resetForm()
    {
        $this->form = [
            'tanggal'           => null,
            'nama_hari_libur'   => '',
            'jenis_hari_libur'  => '',
            'keterangan'        => '',
        ];
        $this->resetValidation();
    }

    public function save()
    {
        $validated = $this->validate()['form'];
        HariLibur::create([
            'tanggal'           => Carbon::createFromFormat('d/m/Y', $validated['tanggal'])->format('Y-m-d'),
            'nama_hari_libur'   => $validated['nama_hari_libur'],
            'jenis_hari_libur'  => $validated['jenis_hari_libur'],
            'keterangan'        => $validated['keterangan'],
        ]);

        $this->resetForm();
        $this->dispatch('close-add-modal');
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Hari libur berhasil ditambahkan.'
        );
        $this->dispatch('refresh-calendar');
    }

    public function render()
    {
        return view('livewire.config.kalender.tambah-hari-libur');
    }
}
