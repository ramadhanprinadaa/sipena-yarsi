<?php

namespace App\Livewire\Manajemen\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SuratPerintahLembur;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EditSpl extends Component
{

    public $open = false;
    public $splId;
    public $searchPegawai = '';
    public $pegawaiResults = [];
    public $selectedEmployees = [];

    public $form = [
        'nomor_surat' => '',
        'unit_kerja' => '',
        'tanggal_dibuat' => '',
        'nama_kegiatan' => '',
        'deskripsi_tugas' => '',
        'jenis_hari' => '',
        'tanggal_lembur' => '',
        'jam_mulai' => '',
        'jam_selesai' => '',
    ];

    #[On('open-edit-spl')]
    public function open($id) {
        $this->splId = $id;
        $this->loadSPL();
        $this->resetValidation();

        $this->open = true;
    }

    public function close()
    {
        $this->resetValidation();
        $this->resetForm();

        $this->open = false;
    }

    public function resetForm()
    {
        $this->form = [
            'nomor_surat' => '',
            'unit_kerja' => '',
            'tanggal_dibuat' => '',
            'nama_kegiatan' => '',
            'deskripsi_tugas' => '',
            'jenis_hari' => '',
            'tanggal_lembur' => '',
            'jam_mulai' => '',
            'jam_selesai' => '',
        ];

        $this->searchPegawai = '';
        $this->pegawaiResults = [];
        $this->selectedEmployees = [];
        $this->splId = null;
    }

    public function loadSPL()
    {
        $spl = SuratPerintahLembur::with(['pegawai', 'unitKerja'])->find($this->splId);
        if ($spl) {
            $this->form = [
                'nomor_surat' => $spl->nomor_surat,
                'unit_kerja' => $spl->unitKerja ? $spl->unitKerja->name : '',
                'tanggal_dibuat' => $spl->tanggal_dibuat,
                'nama_kegiatan' => $spl->nama_kegiatan,
                'deskripsi_tugas' => $spl->deskripsi_tugas,
                'jenis_hari' => $spl->jenis_hari,
                'tanggal_lembur' => $spl->tanggal_lembur,
                'jam_mulai' => $spl->jam_mulai,
                'jam_selesai' => $spl->jam_selesai,
            ];

            $this->selectedEmployees = $spl->pegawai->map(function ($pegawai) {
                $pegawai->loadMissing('unit_kerja');

                return [
                    'id' => $pegawai->id,
                    'name' => $pegawai->nama,
                    'nip' => $pegawai->nip,
                    'npwp' => $pegawai->npwp,
                    'unit_kerja_id' => $pegawai->unit_kerja_id,
                    'unit_kerja' => $pegawai->unit_kerja?->name,
                ];
            })->toArray();
        }
    }

    public function updatedSearchPegawai()
    {
        if (strlen($this->searchPegawai) > 2) {
            $this->pegawaiResults = $this->targetPegawaiQuery()
                ->where(function ($query) {
                    $query->where('nama', 'like', '%' . $this->searchPegawai . '%')
                        ->orWhere('nip', 'like', '%' . $this->searchPegawai . '%')
                        ->orWhere('npwp', 'like', '%' . $this->searchPegawai . '%');
                })
                ->limit(10)
                ->get(['id', 'nama', 'nip', 'npwp']);
        } else {
            $this->pegawaiResults = [];
        }
    }

    public function selectEmployee($pegawaiId)
    {
        $pegawai = $this->targetPegawaiQuery()
            ->with('unit_kerja')
            ->find($pegawaiId);

        if ($pegawai && !collect($this->selectedEmployees)->contains('id', $pegawai->id)) {
            $employee = [
                'id' => $pegawai->id,
                'name' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'npwp' => $pegawai->npwp,
                'unit_kerja_id' => $pegawai->unit_kerja_id,
                'unit_kerja' => $pegawai->unit_kerja?->name,
            ];

            if ($this->usesSelectedEmployeeUnit()) {
                $this->selectedEmployees = [$employee];
            } else {
                $this->selectedEmployees[] = $employee;
            }

            $this->syncUnitKerjaFromSelection();
        }
    }

    public function removeEmployee($index)
    {
        unset($this->selectedEmployees[$index]);
        $this->selectedEmployees = array_values($this->selectedEmployees);
        $this->syncUnitKerjaFromSelection();
    }

    public function save()
    {
        $this->validate([
            'form.nomor_surat' => 'required|string|max:255',
            'form.unit_kerja' => 'required|string|max:255',
            'form.tanggal_dibuat' => 'required|date',
            'form.nama_kegiatan' => 'required|string|max:255',
            'form.deskripsi_tugas' => 'required|string',
            'form.jenis_hari' => 'required|in:Hari Kerja Normal,Hari Libur Mingguan,Hari Libur Nasional',
            'form.tanggal_lembur' => 'required|date',
            'form.jam_mulai' => 'required',
            'form.jam_selesai' => 'required',
            'selectedEmployees' => $this->usesSelectedEmployeeUnit() ? 'required|array|size:1' : 'required|array|min:1',
        ]);

        $this->validateOvertimeRules();

        $spl = SuratPerintahLembur::find($this->splId);
        if (!$spl) {
            $this->addError('general', 'SPL tidak ditemukan.');
            return;
        }

        $unitKerja = $this->resolveUnitKerjaForSpl();
        if (!$unitKerja) {
            $this->addError('form.unit_kerja', 'Unit kerja tidak ditemukan.');
            return;
        }

        $pegawaiIds = collect($this->selectedEmployees)->pluck('id');
        $validPegawaiCount = $this->targetPegawaiQuery()
            ->whereIn('id', $pegawaiIds)
            ->count();

        if ($validPegawaiCount !== $pegawaiIds->count()) {
            $this->addError('selectedEmployees', 'Pegawai yang dipilih tidak sesuai dengan kewenangan penerbit SPL.');
            return;
        }

        // Update SPL
        $spl->update([
            'nomor_surat' => $this->form['nomor_surat'],
            'unit_kerja_id' => $unitKerja->id,
            'tanggal_dibuat' => $this->form['tanggal_dibuat'],
            'nama_kegiatan' => $this->form['nama_kegiatan'],
            'deskripsi_tugas' => $this->form['deskripsi_tugas'],
            'jenis_hari' => $this->form['jenis_hari'],
            'tanggal_lembur' => $this->form['tanggal_lembur'],
            'jam_mulai' => $this->form['jam_mulai'],
            'jam_selesai' => $this->form['jam_selesai'],
        ]);

        // Sync pegawai
        $spl->pegawai()->sync($pegawaiIds);

        // Emit event untuk refresh tabel
        $this->dispatch('spl-updated');

        $this->close();
    }

    private function targetPegawaiQuery()
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        $query = Pegawai::query()
            ->where('id', '!=', $user?->pegawai?->id)
            ->whereHas('user.role');

        if ($role === 'Rektor') {
            return $query->whereHas('user.role', function ($roleQuery) {
                $roleQuery->where('name', 'Pimpinan');
            })->whereHas('unit_kerja', function ($unitQuery) {
                $unitQuery->where('unit_sdm_id', 2);
            });
        }

        if ($role === 'SDM Universitas') {
            return $query->whereHas('user.role', function ($roleQuery) {
                $roleQuery->where('name', 'Rektor');
            });
        }

        if ($role === 'SDM Yayasan') {
            return $query->whereHas('user.role', function ($roleQuery) {
                $roleQuery->where('name', 'SDM Universitas');
            });
        }

        return $query->where('unit_kerja_id', $user?->pegawai?->unit_kerja_id)
            ->whereDoesntHave('user.role', function ($roleQuery) {
                $roleQuery->whereIn('name', ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan']);
            });
    }

    private function usesSelectedEmployeeUnit(): bool
    {
        return in_array(Auth::user()->role->name ?? '', ['Rektor', 'SDM Universitas', 'SDM Yayasan']);
    }

    private function syncUnitKerjaFromSelection(): void
    {
        if (!$this->usesSelectedEmployeeUnit()) {
            return;
        }

        $this->form['unit_kerja'] = $this->selectedEmployees[0]['unit_kerja'] ?? '';
    }

    private function resolveUnitKerjaForSpl()
    {
        if ($this->usesSelectedEmployeeUnit()) {
            $pegawaiId = $this->selectedEmployees[0]['id'] ?? null;

            return $pegawaiId
                ? Pegawai::with('unit_kerja')->find($pegawaiId)?->unit_kerja
                : null;
        }

        return Auth::user()?->pegawai?->unit_kerja;
    }

    private function validateOvertimeRules(): void
    {
        $start = $this->timeToMinutes($this->form['jam_mulai']);
        $end = $this->timeToMinutes($this->form['jam_selesai']);

        if ($end <= $start) {
            throw ValidationException::withMessages([
                'form.jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.',
            ]);
        }

        $duration = $end - $start;

        if ($this->form['jenis_hari'] === 'Hari Kerja Normal') {
            if ($start < $this->timeToMinutes('16:00') || $end > $this->timeToMinutes('18:00')) {
                throw ValidationException::withMessages([
                    'form.jam_mulai' => 'Hari kerja normal hanya boleh antara pukul 16:00 sampai 18:00 WIB.',
                    'form.jam_selesai' => 'Hari kerja normal tidak boleh melebihi pukul 18:00 WIB.',
                ]);
            }

            if ($duration > 120) {
                throw ValidationException::withMessages([
                    'form.jam_selesai' => 'Hari kerja normal maksimal 2 jam.',
                ]);
            }
        }

        if (in_array($this->form['jenis_hari'], ['Hari Libur Mingguan', 'Hari Libur Nasional']) && $duration > 300) {
            throw ValidationException::withMessages([
                'form.jam_selesai' => 'Hari libur mingguan/nasional maksimal 5 jam.',
            ]);
        }
    }

    private function timeToMinutes(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hour * 60) + $minute;
    }

    public function render()
    {
        return view('livewire.manajemen.lembur.edit-spl');
    }
}