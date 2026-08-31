<?php

namespace App\Livewire\Config\UnitKerja;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\UnitKerja;
use App\Models\Pegawai;
use Illuminate\Validation\Rule;


class DetailUnitKerja extends Component
{
    public $editMode = false;

    public $unit;
    public $unitKerja;

    public $pimpinanSearch = '';
    public $pimpinanResults = [];
    public $unitIndukSearch = '';
    public $unitIndukResults = [];

    public $originalForm = [];
    public $form = [
        'name'        => '',
        'unit_sdm_id' => '',
        'pimpinan_id' => '',
        'parent_id'   => '',
    ];

    #[On('load-detail-modal')]
    public function loadData($id)
    {
        $this->unit = UnitKerja::with(['pimpinan', 'parent', 'unitSdm'])->find($id);
        if (! $this->unit) {
            $this->dispatch('notify', type: 'error', message: 'Unit kerja tidak ditemukan.');
            return;
        }
        $this->pimpinanSearch = $this->unit->pimpinan ? $this->unit->pimpinan->nama . ' - ' . $this->unit->pimpinan->nip : '';
        $this->unitIndukSearch = $this->unit->parent ? $this->unit->parent->name : '';
        $this->form = [
            'name'        => $this->unit->name,
            'unit_sdm_id' => $this->unit->unit_sdm_id,
            'pimpinan_id' => $this->unit->pimpinan_id,
            'parent_id'   => $this->unit->parent_id,
        ];
        $this->originalForm = $this->form;
        $this->dispatch('open-detail');
    }

    public function enterEditMode()
    {
        $this->editMode = true;
    }

    public function cancelEdit(): void
    {
        $this->editMode = false;
        $this->fillForm();
        $this->resetValidation();
        $this->pimpinanResults  = [];
        $this->unitIndukResults = [];
    }

    private function fillForm()
    {
        $this->form = [
            'name'        => $this->unit->name,
            'unit_sdm_id' => $this->unit->unit_sdm_id,
            'pimpinan_id' => $this->unit->pimpinan_id,
            'parent_id'   => $this->unit->parent_id,
        ];
        $this->pimpinanSearch  = $this->unit->pimpinan
            ? $this->unit->pimpinan->nama . ' - ' . $this->unit->pimpinan->nip
            : '';

        $this->unitIndukSearch = $this->unit->parent
            ? $this->unit->parent->name
            : '';

        $this->originalForm = $this->form;
    }

    // Pimpinan Search
    public function updatedPimpinanSearch()
    {
        if (blank($this->pimpinanSearch)) {
            $this->pimpinanResults = [];
            return;
        }

        $this->pimpinanResults = Pegawai::query()
            ->where(function ($q) {
                $q->whereDoesntHave('memimpin_unit')
                    ->orWhere('id', $this->unit->pimpinan_id);
            })
            ->where(function ($q) {
                $q->where('nama', 'like', "%{$this->pimpinanSearch}%")
                    ->orWhere('nip', 'like', "%{$this->pimpinanSearch}%");
            })
            ->select('id', 'nama', 'nip')->limit(10)->get();
    }

    public function selectPimpinan($id)
    {
        $pimpinan = Pegawai::find($id);
        if (!$pimpinan) return;
        $this->form['pimpinan_id'] = $pimpinan->id;
        $this->pimpinanSearch = $pimpinan->nama . ' - ' . $pimpinan->nip;
        $this->pimpinanResults = [];
    }

    public function restorePimpinan()
    {
        $current = Pegawai::find($this->form['pimpinan_id']);
        $validText = $current ? $current->nama . ' - ' . $current->nip : '';

        if ($this->pimpinanSearch !== $validText) {
            $this->pimpinanSearch = $validText;
        }
        $this->pimpinanResults = [];
    }

    public function removePimpinan()
    {
        $this->form['pimpinan_id'] = null;
        $this->pimpinanSearch = '';
        $this->pimpinanResults = [];
    }

    // Unit Induk Search
    public function updatedUnitIndukSearch()
    {
        if (blank($this->unitIndukSearch)) {
            $this->unitIndukResults = [];
            return;
        }

        $this->unitIndukResults = UnitKerja::query()
            ->where('id', '!=', $this->unit->id)
            ->where('name', 'like', "%{$this->unitIndukSearch}%")
            ->select('id', 'name')->limit(10)->get();
    }

    public function selectUnitInduk($id)
    {
        $unitInduk = UnitKerja::select('id', 'name')->find($id);
        if (! $unitInduk) return;
        $this->form['parent_id'] = $unitInduk->id;
        $this->unitIndukSearch   = $unitInduk->name;
        $this->unitIndukResults  = [];
    }

    public function restoreUnitInduk()
    {
        $current = UnitKerja::find($this->form['parent_id']);
        $validText = $current ? $current->name  : '';

        if ($this->unitIndukSearch !== $validText) {
            $this->unitIndukSearch = $validText;
        }
        $this->unitIndukResults = [];
    }

    public function removeUnitInduk()
    {
        $this->form['parent_id'] = null;
        $this->unitIndukSearch = '';
        $this->unitIndukResults = [];
    }
    protected function rules()
    {
        return [
            'form.name'        => ['required', 'string', 'max:255'],
            'form.unit_sdm_id' => ['required', 'exists:unit_sdm,id'],
            'form.pimpinan_id' => ['nullable', 'exists:pegawai,id', Rule::unique('unit_kerja', 'pimpinan_id')->ignore($this->unit->id)],
            'form.parent_id'   => ['nullable', 'exists:unit_kerja,id', Rule::unique('unit_kerja', 'parent_id')->ignore($this->unit->id)],
        ];
    }

    protected function messages()
    {
        return [
            'form.name.required' => 'Nama unit wajib diisi.',
            'form.name.unique' => 'Nama unit sudah digunakan.',
            'form.name.max' => 'Nama unit maksimal 255 karakter.',
            'form.unit_sdm_id.required' => 'Unit SDM wajib dipilih.',
            'form.pimpinan_id.exists' => 'Pimpinan tidak valid, silakan pilih dari daftar.',
            'form.parent_id.exists' => 'Unit induk tidak valid, silahkan pilid dari daftar.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.name' => 'Nama Unit',
            'form.unit_sdm_id' => 'Unit SDM',
            'form.pimpinan_id' => 'Pimpinan',
            'form.parent_id' => 'Unit Induk',
        ];
    }

    public function getIsDirtyProperty()
    {
        return $this->form != $this->originalForm;
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    public function save()
    {
        $dirty = collect($this->form)
            ->filter(fn($value, $key) => $value != $this->originalForm[$key])
            ->toArray();

        if (empty($dirty)) return;

        $this->validate();
        $this->unit->update($dirty);
        $this->originalForm = $this->form;

        $this->editMode         = false;
        $this->pimpinanResults  = [];
        $this->unitIndukResults = [];
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: 'Unit kerja berhasil diperbarui.');
        $this->dispatch('refresh-table');
    }

    #[On('close-detail')]
    public function close()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.config.unit-kerja.detail-unit-kerja');
    }
}
