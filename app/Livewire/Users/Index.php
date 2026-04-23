<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $selectedRole;
    public $selectedStatus;

    public $selectedUser;
    public $showDetail = false;

    public $roles;
    public $roleColors = [
        'Admin' => 'bg-red-100 text-red-700',
        'SDM Yayasan' => 'bg-purple-100 text-purple-700',
        'SDM Universitas' => 'bg-indigo-100 text-indigo-700',
        'Rektor' => 'bg-rose-100 text-rose-700',
        'Pimpinan' => 'bg-amber-100 text-amber-700',
        'Staff' => 'bg-blue-100 text-blue-700',
        'Tendik' => 'bg-cyan-100 text-cyan-700',
        'Dosen' => 'bg-emerald-100 text-emerald-700',
        'default' => 'bg-gray-100 text-gray-700'
    ];

    public function mount()
    {
        $this->roles = Role::where('id', '!=', 1)->get();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $users = User::with(['role', 'pegawai'])
            ->where('role_id', '!=', 1)
            ->oldest();

        if ($this->selectedRole) {
            $users->whereHas('role', function ($q) {
                $q->where('name', $this->selectedRole);
            });
        }
        if ($this->selectedStatus) {
            $users->where('status', $this->selectedStatus);
        }
        if ($this->search) {
            $users->where('role_id', '!=', 1)
                ->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('pegawai', function ($q) {
                            $q->where('nip', 'like', '%' . $this->search . '%')
                                ->orWhere('nama', 'like', '%' . $this->search . '%');
                        });
                });
        }
        return view('livewire.users.index', [
            'users' => $users->paginate(4),
            'roleColors' => $this->roleColors,
        ]);
    }

    public function openDetail($id)
    {
        $this->dispatch('open-user-detail', $id);
        $this->showDetail = true;
    }
    #[On('refresh-table')]
    public function refreshTable() {}
}