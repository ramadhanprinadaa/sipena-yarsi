<?php

namespace App\Livewire\Manajemen\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SuratPerintahLembur;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Support\WorkflowEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AddSpl extends Component
{

    public $open = false;

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

    #[On('open-add-spl')]
    public function open() {
        $this->resetForm();
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

        $user = Auth::user();
        $this->form['unit_kerja'] = $this->usesSelectedEmployeeUnit()
            ? ''
            : ($user?->pegawai?->unit_kerja?->name ?? '');

        $this->searchPegawai = '';
        $this->pegawaiResults = [];
        $this->selectedEmployees = [];
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
            'form.nomor_surat' => 'required|unique:surat_perintah_lembur,nomor_surat|string|max:255',
            'form.unit_kerja' => 'required|string|max:255',
            'form.tanggal_dibuat' => 'required|date|after_or_equal:today',
            'form.nama_kegiatan' => 'required|string|max:255',
            'form.deskripsi_tugas' => 'required|string',
            'form.jenis_hari' => 'required|in:Hari Kerja Normal,Hari Libur Mingguan,Hari Libur Nasional',
            'form.tanggal_lembur' => 'required|date|after_or_equal:today',
            'form.jam_mulai' => 'required',
            'form.jam_selesai' => 'required',
            'selectedEmployees' => $this->usesSelectedEmployeeUnit() ? 'required|array|size:1' : 'required|array|min:1',
        ], [], $this->validationAttributes());

        $this->validateOvertimeRules();

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

        if ($this->form['tanggal_lembur'] && $this->form['jenis_hari']) {

            $tanggalLembur = \Carbon\Carbon::parse($this->form['tanggal_lembur']);
            
            // jika jenis hari 'Hari Kerja Normal'
            if ($this->form['jenis_hari'] === 'Hari Kerja Normal' && $tanggalLembur->isWeekend()) {
                $this->addError('form.tanggal_lembur', 'Untuk Hari Kerja Normal, tanggal lembur harus dipilih dari Senin - Jumat.');
                return;
            }

            // jika jenis hari adalah 'Hari Kerja Mingguan'
            if ($this->form['jenis_hari'] === 'Hari Libur Mingguan' && $tanggalLembur->isWeekday()) {
                $this->addError('form.tanggal_lembur', 'Untuk Hari Libur Mingguan, tanggal lembur harus dipilih pada hari Sabtu atau Minggu.');
                return;
            }
            
        }

        // Simpan SPL
        $spl = SuratPerintahLembur::create([
            'nomor_surat' => $this->form['nomor_surat'],
            'unit_kerja_id' => $unitKerja->id,
            'tanggal_dibuat' => $this->form['tanggal_dibuat'],
            'nama_kegiatan' => $this->form['nama_kegiatan'],
            'deskripsi_tugas' => $this->form['deskripsi_tugas'],
            'jenis_hari' => $this->form['jenis_hari'],
            'tanggal_lembur' => $this->form['tanggal_lembur'],
            'jam_mulai' => $this->form['jam_mulai'],
            'jam_selesai' => $this->form['jam_selesai'],
            'status' => 'Diterbitkan',
        ]);

        // Attach pegawai
        $spl->pegawai()->attach($pegawaiIds);
        $spl->load(['pegawai.unit_kerja', 'pegawai.user', 'unitKerja']);

        foreach ($spl->pegawai as $pegawai) {
            WorkflowEmail::notifySplPublished($spl, $pegawai, Auth::user()?->pegawai);
        }

        // Emit event untuk refresh tabel
        $this->dispatch('spl-created');

        $this->close();
    }

    private function targetPegawaiQuery()
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';
        $unitKerjaName = $user?->pegawai?->unit_kerja?->name ?? '';

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
            $unitSdmId = $user?->pegawai?->unit_kerja?->unit_sdm_id;

            // SDM Yayasan bisa menambahkan Pimpinan (sesuai unit_sdm-nya ATAU dari Sekretariat Universitas)
            return $query->whereHas('user.role', function ($userRoleQuery) {
                $userRoleQuery->where('name', 'Pimpinan');
            })->whereHas('unit_kerja', function ($unitQuery) use ($unitSdmId) {
                $unitQuery->where(function($q) use ($unitSdmId) {
                    $q->where('name', 'Sekretariat Universitas');
                    if ($unitSdmId) {
                        $q->orWhere('unit_sdm_id', $unitSdmId);
                    }
                });
            });
        }

        // Default: Untuk Pimpinan atau role biasa (mengambil pegawai di unit kerjanya)
        return $query->where('unit_kerja_id', $user?->pegawai?->unit_kerja_id)
            ->where(function ($q) use ($role, $unitKerjaName) {
                
                // Secara default, tampilkan pegawai yang bukan Pimpinan/Rektor/SDM
                $q->whereDoesntHave('user.role', function ($roleQuery) {
                    $roleQuery->whereIn('name', ['Pimpinan', 'Rektor', 'SDM Universitas', 'SDM Yayasan']);
                });

                // Rule Tambahan khusus untuk role Pimpinan berdasarkan Unit Kerjanya
                if ($role === 'Pimpinan') {
                    // Jika dia Pimpinan dari Sekretariat Universitas, dia boleh menambahkan SDM Universitas
                    if ($unitKerjaName === 'Sekretariat Universitas') {
                        $q->orWhereHas('user.role', function ($roleQuery) {
                            $roleQuery->where('name', 'SDM Universitas');
                        });
                    }

                    // Jika dia Pimpinan dari Sekretariat Yayasan, dia boleh menambahkan SDM Yayasan
                    if ($unitKerjaName === 'Sekretariat Yayasan') {
                        $q->orWhereHas('user.role', function ($roleQuery) {
                            $roleQuery->where('name', 'SDM Yayasan');
                        });
                    }
                }
            });
    }

    private function usesSelectedEmployeeUnit(): bool
    {
        return in_array(Auth::user()->role->name ?? '', ['Rektor', 'SDM Universitas', 'SDM Yayasan']);
    }

    private function validationAttributes(): array
    {
        return [
            'form.nomor_surat' => 'nomor surat',
            'form.unit_kerja' => 'unit kerja',
            'form.tanggal_dibuat' => 'tanggal dibuat',
            'form.nama_kegiatan' => 'nama kegiatan',
            'form.deskripsi_tugas' => 'deskripsi tugas',
            'form.jenis_hari' => 'jenis hari',
            'form.tanggal_lembur' => 'tanggal lembur',
            'form.jam_mulai' => 'jam mulai',
            'form.jam_selesai' => 'jam selesai',
            'selectedEmployees' => 'pilih pegawai',
        ];
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
        return view('livewire.manajemen.lembur.add-spl');
    }
}