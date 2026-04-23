<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $user;

    public $email;
    public $no_telpon;
    public $jenis_kelamin;

    public function mount()
    {
        $this->user = User::with('pegawai', 'role')
            ->find(Auth::id());

        $this->email = $this->user->email;
        $this->no_telpon = $this->user->pegawai?->no_telpon ?? "-";
        $this->jenis_kelamin = $this->user->pegawai?->jenis_kelamin ?? "-";
    }


    public function resetForm()
    {
        $this->email = $this->user->email;
        $this->no_telpon = $this->user->pegawai?->no_telpon;
        $this->jenis_kelamin = $this->user->pegawai?->jenis_kelamin;
    }


    public function save()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'no_telpon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P'
        ]);

        $this->user->update([
            'email' => $this->email
        ]);

        $this->user->pegawai->update([
            'no_telpon' => $this->no_telpon,
            'jenis_kelamin' => $this->jenis_kelamin
        ]);

        session()->flash('success', 'Profil berhasil diperbarui');

        $this->mount();
    }


    public function render()
    {
        return view('livewire.profile');
    }
}