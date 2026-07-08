<?php

namespace App\Livewire\Dashboard\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SuratPerintahLembur;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AddLembur extends Component
{
    public $open = false;
    public $isAutoFilled = false;
    public $availableSPLs = [];

    public $form = [
        'surat_perintah_lembur_id' => '',
        'kegiatan' => '',
        'jam_mulai' => '',
        'jam_selesai' => '',
        'jenis_hari' => '',
        'tanggal_lembur' => '',
    ];

    public function mount()
    {
        $this->loadAvailableSPLs();
    }

    public function loadAvailableSPLs()
    {
        $user = Auth::user();
        if ($user->pegawai) {
            $this->availableSPLs = SuratPerintahLembur::where('status', 'Diterbitkan')
                ->whereHas('pegawai', function ($query) use ($user) {
                    $query->where('pegawai.id', $user->pegawai->id);
                })
                ->whereDoesntHave('lembur', function ($query) use ($user) {
                    $query->where('pegawai_id', $user->pegawai->id);
                })
                ->with('unitKerja')
                ->get()
                ->map(function ($spl) {
                    return [
                        'id' => $spl->id,
                        'nomor' => $spl->nomor_surat,
                        'kegiatan' => $spl->nama_kegiatan,
                        'jam_mulai' => $spl->jam_mulai,
                        'jam_selesai' => $spl->jam_selesai,
                        'jenis_hari' => $spl->jenis_hari,
                        'tanggal_lembur' => $spl->tanggal_lembur,
                    ];
                })
                ->toArray();
        }
    }

    public function updatedFormSuratPerintahLemburId()
    {
        // Find SPL and populate fields
        if ($this->form['surat_perintah_lembur_id']) {
            $spl = SuratPerintahLembur::find($this->form['surat_perintah_lembur_id']);
            if ($spl) {
                $this->form['kegiatan'] = $spl->nama_kegiatan;
                $this->form['jam_mulai'] = $spl->jam_mulai;
                $this->form['jam_selesai'] = $spl->jam_selesai;
                $this->form['jenis_hari'] = $spl->jenis_hari;
                $this->form['tanggal_lembur'] = $spl->tanggal_lembur;
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.lembur.add-pengajuan-lembur');
    }

    #[On('open-add-pengajuan-lembur')]
    public function open() {
        // $this->resetForm();
        // $this->loadAvailableSPLs();
        $this->open = true;
    }

    #[On('fillFormFromSpl')]
    public function fillFormFromSpl($data)
    {
        $this->form['surat_perintah_lembur_id'] = $data['splId'];
        $this->form['jam_mulai'] = $data['jamMulai'];
        $this->form['jam_selesai'] = $data['jamSelesai'];
        $this->form['tanggal_lembur'] = $data['tanggalLembur'];
        $this->form['jenis_hari'] = $data['jenisHari'];
        $this->form['kegiatan'] = $data['kegiatan'];
        $this->isAutoFilled = true;
    }

    public function close()
    {
        $this->resetForm();
        $this->isAutoFilled = false;
        $this->open = false;
    }

    public function resetForm()
    {
        $this->form = [
            'surat_perintah_lembur_id' => '',
            'kegiatan' => '',
            'jam_mulai' => '',
            'jam_selesai' => '',
            'jenis_hari' => '',
            'tanggal_lembur' => '',
        ];
    }

    public function submit()
    {
        $this->validate([
            'form.surat_perintah_lembur_id' => 'required|exists:surat_perintah_lembur,id',
            'form.kegiatan' => 'required|string',
            'form.jam_mulai' => 'required',
            'form.jam_selesai' => 'required',
            'form.jenis_hari' => 'required|in:Hari Kerja Normal,Hari Libur Mingguan,Hari Libur Nasional',
            'form.tanggal_lembur' => 'required|date',
        ]);

        // $spl = SuratPerintahLembur::find($this->form['surat_perintah_lembur_id']);
        // if (!$spl) {
        //     $this->addError('form.surat_perintah_lembur_id', 'SPL tidak ditemukan.');
        //     return;
        // }

        // $this->form['kegiatan'] = $spl->nama_kegiatan;
        // $this->form['jam_mulai'] = $spl->jam_mulai;
        // $this->form['jam_selesai'] = $spl->jam_selesai;
        // $this->form['jenis_hari'] = $spl->jenis_hari;
        // $this->form['tanggal_lembur'] = $spl->tanggal_lembur;

        $this->validateOvertimeRules();

        $user = Auth::user();
        if (!$user->pegawai) {
            $this->addError('general', 'Pengguna tidak memiliki data pegawai.');
            return;
        }

        // $alreadySubmitted = Lembur::where('pegawai_id', $user->pegawai->id)
        //     ->where('surat_perintah_lembur_id', $this->form['surat_perintah_lembur_id'])
        //     ->exists();

        // if ($alreadySubmitted) {
        //     $this->addError('form.surat_perintah_lembur_id', 'Anda sudah mengajukan lembur untuk SPL ini.');
        //     $this->loadAvailableSPLs();
        //     return;
        // }

        $lembur = new Lembur([
            'pegawai_id' => $user->pegawai->id,
            'surat_perintah_lembur_id' => $this->form['surat_perintah_lembur_id'],
            'tanggal_lembur' => $this->form['tanggal_lembur'],
            'jenis_hari' => $this->form['jenis_hari'],
            'jam_mulai' => $this->form['jam_mulai'],
            'jam_selesai' => $this->form['jam_selesai'],
            'alasan_lembur' => $this->form['kegiatan'],
        ]);
        $lembur->setRelation('pegawai', $user->pegawai->loadMissing(['unit_kerja', 'user.role']));
        $lembur->status = 'Menunggu Pelaksanaan';
        // $lembur->status = $this->initialApprovalStatusFor($lembur);
        $lembur->save();

        // Emit event untuk refresh data
        $this->dispatch('lembur-created');
        $this->close();
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

    // private function initialApprovalStatusFor(Lembur $lembur): string
    // {
    //     $role = $lembur->pegawai?->user?->role?->name;
    //     $unitSdmId = (int) $lembur->pegawai?->unit_kerja?->unit_sdm_id;

    //     if ($role === 'Pimpinan') {
    //         return $unitSdmId === 1
    //             ? 'Menunggu Verifikasi SDM Yayasan'
    //             : 'Menunggu Verifikasi Rektor';
    //     }

    //     if ($role === 'Rektor') {
    //         return 'Menunggu Verifikasi SDM Universitas';
    //     }

    //     if ($role === 'SDM Universitas') {
    //         return 'Menunggu Verifikasi SDM Yayasan';
    //     }

    //     return 'Menunggu Verifikasi Atasan';
    // }

}