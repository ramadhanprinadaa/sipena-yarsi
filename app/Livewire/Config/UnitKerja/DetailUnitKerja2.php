<?php

namespace App\Livewire\Config\UnitKerja;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\UnitKerja;
use App\Models\Pegawai;

class DetailUnitKerja2 extends Component
{
    public ?int $unitId = null;
    public $unit = null;

    public bool $editMode = false;

    public array $form = [
        'nama_unit'     => '',
        'unit_sdm_id'   => null,
        'pimpinan_id'   => null,
        'unit_induk_id' => null,
    ];

    protected array $originalForm = [];

    // Pimpinan typeahead
    public string $pimpinanSearch = '';
    public $pimpinanResults = [];

    // Unit Induk typeahead
    public string $unitIndukSearch = '';
    public $unitIndukResults = [];

    // ─── Validation ───────────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'form.nama_unit'     => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('unit_kerja', 'name')
                    ->ignore($this->unitId)
            ],
            'form.unit_sdm_id'   => ['required', 'exists:unit_sdm,id'],
            'form.pimpinan_id'   => ['nullable', 'exists:pegawai,id'],
            'form.unit_induk_id' => ['nullable', 'exists:unit_kerja,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'form.nama_unit.required'    => 'Nama unit wajib diisi.',
            'form.nama_unit.unique'      => 'Nama unit sudah digunakan.',
            'form.nama_unit.max'         => 'Nama unit maksimal 255 karakter.',
            'form.unit_sdm_id.required'  => 'Unit SDM wajib dipilih.',
            'form.pimpinan_id.exists'    => 'Pimpinan tidak valid, silakan pilih dari daftar.',
            'form.unit_induk_id.exists'  => 'Unit induk tidak valid.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.nama_unit'     => 'nama unit',
            'form.unit_sdm_id'   => 'unit SDM',
            'form.pimpinan_id'   => 'pimpinan',
            'form.unit_induk_id' => 'unit induk',
        ];
    }

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Dipanggil oleh TabelUnitKerja::openDetail().
     * Data dimuat dulu, baru dispatch 'open-detail' agar modal Alpine
     * terbuka hanya setelah konten siap.
     */
    #[On('show-detail')]
    public function loadData(int $id): void
    {
        $this->reset([
            'unit',
            'form',
            'unitId',
            'editMode',
            'pimpinanSearch',
            'pimpinanResults',
            'unitIndukSearch',
            'unitIndukResults'
        ]);
        $this->resetValidation();

        $this->unit = UnitKerja::with(['pimpinan', 'parent', 'unitSdm'])->find($id);

        if (! $this->unit) {
            $this->dispatch('notify', type: 'error', message: 'Unit kerja tidak ditemukan.');
            return;
        }

        $this->unitId = $id;
        $this->fillForm();

        // Dispatch SETELAH data siap — Alpine parent baru membuka modal
        $this->dispatch('open-detail');
    }

    private function fillForm(): void
    {
        $this->form['nama_unit']     = $this->unit->name;
        $this->form['unit_sdm_id']   = $this->unit->unit_sdm_id ? (int) $this->unit->unit_sdm_id : null;
        $this->form['pimpinan_id']   = $this->unit->pimpinan_id ? (int) $this->unit->pimpinan_id : null;
        $this->form['unit_induk_id'] = $this->unit->parent_id   ? (int) $this->unit->parent_id   : null;

        $this->pimpinanSearch  = $this->unit->pimpinan
            ? $this->unit->pimpinan->nama . ' - ' . $this->unit->pimpinan->nip
            : '';

        $this->unitIndukSearch = $this->unit->unitInduk
            ? $this->unit->unitInduk->name
            : '';

        $this->originalForm = $this->form;
    }

    // ─── Mode Toggle ──────────────────────────────────────────────────────────

    public function enterEditMode(): void
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

    public function close(): void
    {
        $this->reset();
        $this->dispatch('close-detail');
    }

    // ─── Computed Properties ──────────────────────────────────────────────────
    public function getIsDirtyProperty(): bool
    {
        return $this->form !== $this->originalForm;
    }

    // ─── Real-time Validation ─────────────────────────────────────────────────

    public function updated(string $property): void
    {
        foreach (['unit_sdm_id', 'pimpinan_id', 'unit_induk_id'] as $key) {
            if (isset($this->form[$key]) && $this->form[$key] !== null) {
                $this->form[$key] = (int) $this->form[$key];
            }
        }

        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    // ─── Pimpinan Typeahead ───────────────────────────────────────────────────

    public function updatedPimpinanSearch(): void
    {
        if (blank($this->pimpinanSearch)) {
            $this->pimpinanResults  = [];
            $this->form['pimpinan_id'] = null;
            return;
        }

        $search = $this->pimpinanSearch;

        // Sertakan pimpinan aktif unit ini agar bisa dipilih ulang
        $this->pimpinanResults = Pegawai::query()
            ->select('id', 'nama', 'nip')
            ->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip',  'like', "%{$search}%");
            })
            ->where(function ($q) {
                $q->whereDoesntHave('memimpin_unit')
                    ->orWhere('id', $this->form['pimpinan_id']);
            })
            ->limit(10)
            ->get();
    }

    public function selectPimpinan(int $id): void
    {
        $pimpinan = Pegawai::select('id', 'nama', 'nip')->find($id);
        if (! $pimpinan) return;

        $this->form['pimpinan_id'] = $pimpinan->id;
        $this->pimpinanSearch      = $pimpinan->nama . ' - ' . $pimpinan->nip;
        $this->pimpinanResults     = [];
    }

    public function removePimpinan(): void
    {
        $this->form['pimpinan_id'] = null;
        $this->pimpinanSearch      = '';
        $this->pimpinanResults     = [];
    }

    // ─── Unit Induk Typeahead ─────────────────────────────────────────────────

    public function updatedUnitIndukSearch(): void
    {
        if (blank($this->unitIndukSearch)) {
            $this->unitIndukResults     = [];
            $this->form['unit_induk_id'] = null;
            return;
        }

        $search = $this->unitIndukSearch;
        $selfId = $this->unitId;

        $this->unitIndukResults = UnitKerja::query()
            ->select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->where('id', '!=', $selfId)    // tidak boleh memilih diri sendiri
            ->limit(10)
            ->get();
    }

    public function selectUnitInduk(int $id): void
    {
        $unitInduk = UnitKerja::select('id', 'name')->find($id);
        if (! $unitInduk) return;

        $this->form['unit_induk_id'] = $unitInduk->id;
        $this->unitIndukSearch       = $unitInduk->name;
        $this->unitIndukResults      = [];
    }

    public function removeUnitInduk(): void
    {
        $this->form['unit_induk_id'] = null;
        $this->unitIndukSearch       = '';
        $this->unitIndukResults      = [];
    }

    // ─── Save ─────────────────────────────────────────────────────────────────

    public function save(): void
    {
        // Validasi manual: search diisi tapi belum dipilih dari dropdown
        if (! empty($this->pimpinanSearch) && empty($this->form['pimpinan_id'])) {
            $this->addError('form.pimpinan_id', 'Pimpinan tidak valid, pilih dari daftar yang tersedia.');
            $this->pimpinanSearch = '';
            return;
        }

        if (! empty($this->unitIndukSearch) && empty($this->form['unit_induk_id'])) {
            $this->addError('form.unit_induk_id', 'Unit induk tidak valid, pilih dari daftar yang tersedia.');
            $this->unitIndukSearch = '';
            return;
        }

        $validated = $this->validate()['form'];

        $this->unit->update([
            'name'          => $validated['nama_unit'],
            'unit_sdm_id'   => $validated['unit_sdm_id'],
            'pimpinan_id'   => $validated['pimpinan_id'],
            'parent_id'     => $validated['unit_induk_id'],
        ]);

        // Reload relasi supaya tampilan info terupdate
        $this->unit->refresh()->load(['pimpinan', 'parent', 'unitSdm']);

        $this->fillForm();

        $this->editMode         = false;
        $this->pimpinanResults  = [];
        $this->unitIndukResults = [];
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: 'Unit kerja berhasil diperbarui.');
        $this->dispatch('refresh-table');
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.config.unit-kerja.detail-unit-kerja2', [
            'unit' => $this->unit,
            'isDirty' => $this->isDirty,
        ]);
    }
}