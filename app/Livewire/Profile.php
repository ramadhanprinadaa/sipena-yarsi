<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $userId;
    public $user;

    public $form = [
        'email' => '',
        'no_telpon' => '',
        'jenis_kelamin' => ''
    ];

    public $edit;

    public $dataOriginal = [];

    public function mount()
    {
        $this->userId = Auth::id();
        $this->user = User::with('pegawai', 'role')->find($this->userId);

        $this->form = [
            'email' => $this->user->email,
            'no_telpon' => $this->user->pegawai?->no_telpon,
            'jenis_kelamin' => $this->user->pegawai?->jenis_kelamin,
        ];
        $this->dataOriginal = $this->form;
    }

    protected function rules() {
        return [
            'form.email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->id)],
            'form.no_telpon' => ['nullable', 'string', 'digits_between:10,15', 'regex:/^[0-9]{10,15}$/'],
            'form.jenis_kelamin' => ['nullable', 'in:L,P'],
        ];
    }

    protected function messages()
    {
        return [
            'form.email.required' => 'Email wajib diisi.',
            'form.email.email'    => 'Format email tidak valid.',
            'form.email.unique'   => 'Email sudah terdaftar.',

            'form.no_telpon.digits_between' => 'Nomor telepon harus terdiri antara 10 hingga 15 digit.',
            'form.no_telpon.regex' => 'Nomor telepon tidak valid.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.email' => 'email',
            'form.no_telpon' => 'nomor telepon',
            'form.jenis_kelamin' => 'jenis kelamin',
        ];
    }

    // computed property
    public function getIsDirtyProperty()
    {
        return $this->form !== $this->dataOriginal;
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'form')) {
            $this->validateOnly($property);
        }
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->form = $this->dataOriginal;
    }

    public function cancel()
    {
        $this->resetForm();
        $this->refreshUser();
    }

    public function save()
    {
        if (!$this->isDirty) {
            return;
        }

        $this->validate();

        if ($this->user->email !== $this->form['email']) {
            $this->user->update([
                'email' => $this->form['email']
            ]);
        }

        if ($this->user->pegawai) {
            $this->user->pegawai->update([
                'no_telpon' => $this->form['no_telpon'],
                'jenis_kelamin' => $this->form['jenis_kelamin'],
            ]);
        }

        $this->dataOriginal = $this->form;

        $this->resetValidation();
        $this->dispatch('profile-saved');
        session()->flash('success', 'Profil berhasil diperbarui');

        $this->refreshUser();
    }

    private function refreshUser()
    {
        $this->user->refresh()->load('pegawai', 'role');

        $this->form = [
            'email' => $this->user->email,
            'no_telpon' => $this->user->pegawai?->no_telpon,
            'jenis_kelamin' => $this->user->pegawai?->jenis_kelamin,
        ];

        $this->dataOriginal = $this->form;
    }

    public function render()
    {
        return view('livewire.profile');
    }

}