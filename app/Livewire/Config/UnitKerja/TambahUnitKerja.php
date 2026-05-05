<?php

namespace App\Livewire\Config\UnitKerja;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;

use App\Models\UnitKerja;
use App\Models\Pegawai;

class TambahUnitKerja extends Component
{
    public $form = [
        'nama_unit' => '',
        'unit_sdm_id' => null,
        'pimpinan_id' => null,
        'unit_induk_id' => null,
    ];

    public $pimpinanSearch = '';
    public $pimpinanResults = [];

    public $unitIndukSearch = '';
    public $unitIndukResults = [];

    #[On('close-modal')]
    public function handleClose()
    {
        $this->resetForm();
    }

    protected function rules()
    {
        return [
            'form.nama_unit' => ['required', 'unique:unit_kerja,name', 'string', 'max:255'],
            'form.unit_sdm_id' => ['required', 'exists:unit_sdm,id'],
            'form.pimpinan_id' => ['nullable', 'exists:pegawai,id'],
            'form.unit_induk_id' => ['nullable', 'exists:unit_kerja,id'],
        ];
    }

    protected function messages()
    {
        return [
            'form.nama_unit.required' => 'Nama unit wajib diisi.',
            'form.nama_unit.unique' => 'Nama unit sudah digunakan.',
            'form.nama_unit.string' => 'Nama unit harus berupa teks huruf A-Z.',
            'form.nama_unit.max' => 'Nama unit maksimal 100 karakter.',

            'form.unit_sdm_id.required' => 'Unit SDM wajib dipilih.',
            'form.pimpinan_id.exists'   => 'Pimpinan tidak valid, silakan pilih dari daftar.',
            'form.unit_induk_id.exists' => 'Unit induk tidak valid.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.nama_unit' => 'nama unit',
            'form.unit_sdm_id' => 'unit SDM',
            'form.pimpinan_id' => 'pimpinan',
            'form.unit_induk_id' => 'unit induk',
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
        $this->reset([
            'form',
            'pimpinanSearch',
            'pimpinanResults',
            'unitIndukSearch',
            'unitIndukResults',
        ]);

        $this->form = [
            'nama_unit' => '',
            'unit_sdm_id' => null,
            'pimpinan_id' => null,
            'unit_induk_id' => null,
        ];

        $this->resetValidation();
    }

    public function updatedPimpinanSearch()
    {
        if (blank($this->pimpinanSearch)) {
            $this->pimpinanResults = [];
            return;
        }

        $search = $this->pimpinanSearch;

        $this->pimpinanResults = Pegawai::query()
            ->select('id', 'nama', 'nip')
            ->whereDoesntHave('memimpin_unit')
            ->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
    }

    public function updatedUnitIndukSearch()
    {
        if (blank($this->unitIndukSearch)) {
            $this->unitIndukResults = [];
            return;
        }

        $search = $this->unitIndukSearch;

        $this->unitIndukResults = UnitKerja::query()
            ->select('id', 'name')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
    }

    public function selectPimpinan($id)
    {
        $pimpinan = Pegawai::query()
            ->select('id', 'nama', 'nip')
            ->find($id);

        if (!$pimpinan) {
            return;
        }

        $this->form['pimpinan_id'] = $pimpinan->id;
        $this->pimpinanSearch = $pimpinan->nama . ' - ' . $pimpinan->nip;
        $this->pimpinanResults = [];
    }
    public function selectUnitInduk($id)
    {
        $unit_induk = UnitKerja::query()
            ->select('id', 'name')
            ->find($id);

        if (!$unit_induk) {
            return;
        }

        $this->form['unit_induk_id'] = $unit_induk->id;
        $this->unitIndukSearch = $unit_induk->name;
        $this->unitIndukResults = [];
    }

    public function removePimpinan()
    {
        $this->form['pimpinan_id'] = null;
        $this->pimpinanSearch = '';
        $this->pimpinanResults = [];
    }

    public function removeUnitInduk()
    {
        $this->form['unit_induk_id'] = null;
        $this->unitIndukSearch = '';
        $this->unitIndukResults = [];
    }

    public function save()
    {

        // manual validation for pimpinan and unit induk
        if (!empty($this->pimpinanSearch) && empty($this->form['pimpinan_id'])) {
            $this->addError('form.pimpinan_id', 'Pimpinan tidak valid, pilih dari daftar yang tersedia.');
            $this->pimpinanSearch = '';
            return;
        }
        if (!empty($this->unitIndukSearch) && empty($this->form['unit_induk_id'])) {
            $this->addError('form.unit_induk_id', 'Unit induk tidak valid, pilih dari daftar yang tersedia.');
            $this->unitIndukSearch = '';
            return;
        }

        $validated = $this->validate()['form'];
        UnitKerja::create([
            'name' => $validated['nama_unit'],
            'unit_sdm_id' => $validated['unit_sdm_id'],
            'pimpinan_id' => $validated['pimpinan_id'],
            'unit_induk_id' => $validated['unit_induk_id'],
        ]);
        $this->resetForm();
        $this->dispatch('close-modal');
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Unit Kerja berhasil ditambahkan'
        );
        $this->dispatch('refresh-table');
    }

    public function render()
    {
        return view('livewire.config.unit-kerja.tambah-unit-kerja');
    }
}
