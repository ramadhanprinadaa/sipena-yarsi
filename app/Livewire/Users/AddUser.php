<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Pegawai;


class AddUser extends Component
{
    public $open = false;

    #[Validate('required|min:8')]
    public $username = '';
    #[Validate('required|email|unique:users,email')]
    public $email = '';
    #[Validate('required|min:8')]
    public $password = '';
    #[Validate('nullable|exists:pegawai,id')]
    public $pegawai_id;
    #[Validate('required|exists:roles,id')]
    public $role;

    public $pegawaiSearch = '';
    public $pegawaiResults = [];

    public $pegawaiList = [];
    public $roleList = [];

    protected $listeners = [
        'open-add-user' => 'open'
    ];

    public function open()
    {
        $this->resetForm();
        $this->open = true;
        $this->pegawaiList = Pegawai::orderBy('nama')->get();
        $this->roleList = Role::orderBy('id')->get();
    }

    public function close()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->open = false;
    }

    public function save()
    {
        $this->validate();

        User::create([
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'pegawai_id' => $this->pegawai_id,
            'role_id' => $this->role,
            'status' => 'active',
        ]);

        $this->open = false;
        $this->resetForm();
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'User berhasil ditambahkan'
        );
    }

    public function resetForm()
    {
        $this->reset([
            'username',
            'email',
            'password',
            'pegawai_id',
            'role'
        ]);
    }

    public function updatedPegawaiSearch()
    {
        $this->pegawaiResults = Pegawai::query()
            ->whereDoesntHave('user')
            ->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->pegawaiSearch . '%')
                    ->orWhere('nip', 'like', '%' . $this->pegawaiSearch . '%');
            })
            ->limit(10)
            ->get();
    }

    public function selectPegawai($id)
    {
        $pegawai = Pegawai::find($id);

        $this->pegawai_id = $pegawai->id;
        $this->pegawaiSearch = $pegawai->nama . ' - ' . $pegawai->nip;

        $this->pegawaiResults = [];
    }

    public function render()
    {
        return view('livewire.users.add-user');
    }
}