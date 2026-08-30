<?php

namespace App\Livewire\Manajemen\Users;

use App\Models\Pegawai;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;


class DetailUser extends Component
{
    public $show = false;
    public $userId;
    public $user; // Tambahkan properti public agar bisa diakses di blade
    public $roles;

    public $pegawaiSearch;
    public $pegawaiResults = [];
    public $pegawaiInitialId;
    public $pegawaiInitialText;

    public $username;
    public $formEdit = [
        'email' => '',
        'role_id' => '',
        'pegawai_id' => '',
        'status' => ''
    ];

    public $dataOriginal = [];

    public function mount()
    {
        $this->roles = Role::whereKeyNot(1)->get(['id', 'name']);
    }

    #[On('load-detail-modal')]
    public function open($userId)
    {
        $this->userId = $userId;
        $this->user = User::with('pegawai', 'role')->find($userId);
        $this->username = $this->user->username;
        $this->pegawaiSearch = $this->user->pegawai
            ? $this->user->pegawai->nama . ' - ' . $this->user->pegawai->nip
            : '';
        $this->formEdit = [
            'email'      => $this->user->email,
            'role_id'    => $this->user->role_id,
            'pegawai_id' => $this->user->pegawai_id,
            'status'     => $this->user->status,
        ];
        $this->pegawaiInitialId   = $this->formEdit['pegawai_id'];
        $this->pegawaiInitialText = $this->pegawaiSearch;
        $this->dataOriginal = $this->formEdit;
        $this->dispatch('open-detail-modal');
    }

    #[Computed]
    public function isDirty()
    {
        return $this->formEdit != $this->dataOriginal;
    }

    #[On('close-detail-modal')]
    public function close()
    {
        $this->resetValidation();
        $this->resetExcept('roles');
    }

    protected function rules()
    {
        $id = $this->userId;
        return [
            'formEdit.email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'formEdit.role_id' => 'required|exists:roles,id',
            'formEdit.pegawai_id' => ['nullable', 'exists:pegawai,id', Rule::unique('users', 'pegawai_id')->ignore($id)],
            'formEdit.status' => 'required|in:active,inactive'
        ];
    }

    public function save()
    {
        $dirty = collect($this->formEdit)
            ->filter(fn($value, $key) => $value != $this->dataOriginal[$key])
            ->toArray();

        if (empty($dirty)) return;

        $this->validate();
        User::whereKey($this->userId)->update($dirty);
        $this->dataOriginal = $this->formEdit;

        $this->dispatch('refresh-table');
        $this->dispatch('notify', type: 'success', message: 'User berhasil diupdate');

        $this->dispatch('close-detail-modal');
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.manajemen.users.detail-user');
    }

    public function getIsDirtyProperty()
    {
        return $this->formEdit != $this->dataOriginal;
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'formEdit')) {
            $this->validateOnly($property);
        }
    }

    public function updatedPegawaiSearch()
    {

        if (blank($this->pegawaiSearch)) {
            $this->pegawaiResults = [];
            return;
        }

        $search = $this->pegawaiSearch;

        $this->pegawaiResults = Pegawai::query()
            ->where(function ($q) {
                $q->whereDoesntHave('user')
                    ->orWhere('id', $this->pegawaiInitialId);
            })
            ->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            })
            ->select('id', 'nama', 'nip')
            ->limit(10)
            ->get();
    }

    public function selectPegawai($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) return;
        $this->formEdit['pegawai_id'] = $pegawai->id;
        $this->pegawaiSearch = $pegawai->nama . ' - ' . $pegawai->nip;
        $this->pegawaiResults = [];
    }

    public function restorePegawai()
    {
        $current = Pegawai::find($this->formEdit['pegawai_id']);

        $validText = $current
            ? $current->nama . ' - ' . $current->nip
            : '';

        if ($this->pegawaiSearch !== $validText) {
            $this->pegawaiSearch = $validText;
        }

        $this->pegawaiResults = [];
    }

    public function removePegawai()
    {
        $this->formEdit['pegawai_id'] = null;
        $this->pegawaiSearch = '';
        $this->pegawaiResults = [];
    }

    protected function messages()
    {
        return [
            'formEdit.email.required' => 'Email wajib diisi.',
            'formEdit.email.email' => 'Masukkan email yang valid.',
            'formEdit.email.unique' => 'Email sudah terdaftar.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'formEdit.email' => 'email',
        ];
    }
}