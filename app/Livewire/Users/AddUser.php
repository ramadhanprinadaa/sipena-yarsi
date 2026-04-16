<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\Pegawai;

class AddUser extends Component
{
    public $open = false;

    public $username;
    public $email;
    public $password;
    public $pegawai_id;
    public $role_id;

    public $pegawaiSearch = '';
    public $pegawaiResults = [];

    public $pegawaiList = [];
    public $roleList = [];

    protected $rules = [
        'username' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'pegawai_id' => 'nullable|exists:pegawai,id',
        'role_id' => 'required|exists:roles,id',
    ];

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

    public function save()
    {
        $this->validate();

        User::create([
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'pegawai_id' => $this->pegawai_id,
            'role_id' => $this->role_id,
            'status' => 'active',
        ]);

        $this->dispatch('user-created');

        $this->open = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'username',
            'email',
            'password',
            'pegawai_id',
            'role_id'
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