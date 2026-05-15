<?php

namespace App\Livewire\Manajemen\Users;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Pegawai;

class AddUser extends Component
{
    public $open = false;
    public $roles = [];
    public $form = [
        'username'   => '',
        'email'      => '',
        'password'   => '',
        'pegawai_id' => null,
        'role_id'    => '',
    ];

    public $pegawaiSearch = '';
    public $pegawaiResults = [];

    #[On('open-add-modal')]
    public function open()
    {
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('close-add-modal')]
    public function close()
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function mount()
    {
        $this->roles = Role::query()
            ->whereKeyNot(1)
            ->orderBy('id')
            ->get(['id', 'name']);
    }

    protected function rules()
    {
        return [
            'form.username' => [
                'required',
                'min:8',
                'unique:users,username',
            ],

            'form.email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'form.password' => [
                'required',
                'min:8',
            ],

            'form.pegawai_id' => [
                'nullable',
                'exists:pegawai,id',
                Rule::unique('users', 'pegawai_id'),
            ],

            'form.role_id' => [
                'required',
                'exists:roles,id',
            ],
        ];
    }

    protected function messages()
    {
        return [
            'form.username.required' => 'Username wajib diisi.',
            'form.username.min'      => 'Username minimal 8 karakter.',
            'form.username.unique'   => 'Username sudah digunakan.',

            'form.email.required' => 'Email wajib diisi.',
            'form.email.email'    => 'Format email tidak valid.',
            'form.email.unique'   => 'Email sudah terdaftar.',

            'form.password.required' => 'Password wajib diisi.',
            'form.password.min'      => 'Password minimal 8 karakter.',

            'form.pegawai_id.unique' => 'Pegawai sudah memiliki akun.',

            'form.role_id.required' => 'Role wajib dipilih.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.username'   => 'username',
            'form.email'      => 'email',
            'form.password'   => 'password',
            'form.pegawai_id' => 'pegawai',
            'form.role_id'    => 'role',
        ];
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    public function save()
    {
        $validated = $this->validate()['form'];

        User::create([
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'pegawai_id' => $validated['pegawai_id'],
            'role_id'    => $validated['role_id'],
            'status'     => 'active',
        ]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'User berhasil ditambahkan'
        );

        $this->dispatch('refresh-table');
        $this->dispatch('close-add-modal');
    }

    public function updatedPegawaiSearch()
    {
        if (blank($this->pegawaiSearch)) {
            $this->pegawaiResults = [];
            return;
        }

        $search = $this->pegawaiSearch;

        $this->pegawaiResults = Pegawai::query()
            ->select('id', 'nama', 'nip')
            ->whereDoesntHave('user')
            ->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
    }

    public function selectPegawai($id)
    {
        $pegawai = Pegawai::query()
            ->select('id', 'nama', 'nip')
            ->find($id);

        if (!$pegawai) {
            return;
        }

        $this->form['pegawai_id'] = $pegawai->id;
        $this->pegawaiSearch = $pegawai->nama . ' - ' . $pegawai->nip;
        $this->pegawaiResults = [];
    }

    public function removePegawai()
    {
        $this->form['pegawai_id'] = null;
        $this->pegawaiSearch = '';
        $this->pegawaiResults = [];
    }

    public function resetForm()
    {
        $this->reset([
            'form',
            'pegawaiSearch',
            'pegawaiResults',
        ]);

        $this->form = [
            'username'   => '',
            'email'      => '',
            'password'   => '',
            'pegawai_id' => null,
            'role_id'    => '',
        ];
    }

    public function render()
    {
        return view('livewire.manajemen.users.add-user');
    }
}