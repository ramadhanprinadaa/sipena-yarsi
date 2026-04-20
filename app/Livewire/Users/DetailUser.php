<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Role;
use App\Models\Pegawai;
use Illuminate\Validation\Rule;


class DetailUser extends Component
{
    public $show = false;
    public $userId;
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

    #[On('open-user-detail')]
    public function open($id)
    {
        $this->show = true;

        $user = User::with('pegawai', 'role')->find($id);

        $this->userId = $id;

        $this->username = $user->username;
        $this->pegawaiSearch = $user->pegawai
            ? $user->pegawai->nama . ' - ' . $user->pegawai->nip
            : '';

        $this->formEdit = [
            'email' => $user->email,
            'role_id' => $user->role_id,
            'pegawai_id' => $user->pegawai_id,
            'status' => $user->status,
        ];

        $this->pegawaiInitialId   = $this->formEdit['pegawai_id'];
        $this->pegawaiInitialText = $this->pegawaiSearch;

        $this->dataOriginal = $this->formEdit;
    }

    public function close()
    {
        $this->resetValidation();
        $this->reset();
        $this->show = false;
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

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.users.detail-user');
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