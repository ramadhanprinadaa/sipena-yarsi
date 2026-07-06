<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Rekening;

use App\Models\Rekening as RekeningModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class RekeningForm extends Component
{
    #[Locked]
    public int $pegawai_id;

    public ?int $recordId = null;

    public bool $showModal = false;

    #[Locked]
    public array $bankOptions = ['Mandiri', 'BCA', 'BRI'];

    public array $form = [
        'nama_bank'      => '',
        'nomor_rekening' => '',
        'nama_rekening'  => '',
    ];

    public array $originalForm = [];

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->loadForm();
    }

    public function loadForm(): void
    {
        $rekening = RekeningModel::where('pegawai_id', $this->pegawai_id)->first();

        if ($rekening) {
            $this->recordId = $rekening->id;
            $this->form = [
                'nama_bank'      => $rekening->nama_bank,
                'nomor_rekening' => $rekening->nomor_rekening,
                'nama_rekening'  => $rekening->nama_rekening,
            ];
        } else {
            $this->recordId = null;
            $this->form = [
                'nama_bank'      => '',
                'nomor_rekening' => '',
                'nama_rekening'  => '',
            ];
        }

        $this->originalForm = $this->form;
    }

    #[On('open-rekening-modal')]
    public function openModal(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->showModal = true;
    }

    #[On('close-rekening-modal')]
    public function closeModal(): void
    {
        $this->resetValidation();
        $this->showModal = false;
        $this->form = $this->originalForm;
    }

    public function updated($property): void
    {
        if (!str_starts_with($property, 'form.')) {
            return;
        }

        $this->validateOnly($property, $this->rules());
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            RekeningModel::updateOrCreate(
                ['pegawai_id' => $this->pegawai_id],
                [
                    'nama_bank'      => $validated['form']['nama_bank'],
                    'nomor_rekening' => $validated['form']['nomor_rekening'],
                    'nama_rekening'  => $validated['form']['nama_rekening'],
                    'updated_by'     => Auth::id(),
                    'updated_at'     => now(),
                ]
            );
        });

        $isEditMode = $this->isEditMode;

        $this->loadForm();
        $this->resetValidation();

        $this->dispatch('rekening-saved');

        $this->dispatch('close-rekening-modal');

        $this->dispatch(
            'notify',
            type: 'success',
            message: $isEditMode
                ? 'Data rekening berhasil diperbarui.'
                : 'Data rekening berhasil ditambahkan.'
        );
    }

    protected function rules(): array
    {
        return [
            'form.nama_bank'      => ['required', 'string', Rule::in($this->bankOptions)],
            'form.nomor_rekening' => ['required', 'digits_between:5,30'],
            'form.nama_rekening'  => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z\s\.\'\-]+$/'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.nama_bank'      => 'Nama Bank',
            'form.nomor_rekening' => 'Nomor Rekening',
            'form.nama_rekening'  => 'Nama Pemilik Rekening',
        ];
    }

    protected function messages(): array
    {
        return [
            // Nama Bank
            'form.nama_bank.required' => ':attribute wajib dipilih.',
            'form.nama_bank.string'   => ':attribute harus berupa teks.',
            'form.nama_bank.in'       => ':attribute yang dipilih tidak valid. Silakan pilih dari daftar yang tersedia.',

            // Nomor Rekening
            'form.nomor_rekening.required'       => ':attribute wajib diisi.',
            'form.nomor_rekening.digits_between' => ':attribute harus berupa angka dengan panjang antara :min sampai :max digit.',

            // Nama Pemilik Rekening
            'form.nama_rekening.required' => ':attribute wajib diisi.',
            'form.nama_rekening.string'   => ':attribute harus berupa teks.',
            'form.nama_rekening.max'      => ':attribute tidak boleh lebih dari :max karakter.',
            'form.nama_rekening.regex'    => ':attribute hanya boleh berisi huruf, spasi, tanda titik (.), apostrof (\'), dan tanda hubung (-).',
        ];
    }

    public function getIsDirtyProperty()
    {
        return $this->form != $this->originalForm;
    }

    #[Computed]
    public function isEditMode(): bool
    {
        return $this->recordId !== null;
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.rekening.rekening-form');
    }
}