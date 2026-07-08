<?php

namespace App\Livewire\Dashboard\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Lembur;
use App\Models\LaporanHasilLembur;
use App\Support\WorkflowEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;



class AddLaporan extends Component
{

    use WithFileUploads;

    public $open = false;
    public $isAutoFilled = false;
    public $availableLembur = [];
    public $dokumen_laporan = null;

    public $form = [
        'lembur_id' => '',
        'jam_mulai' => '',
        'jam_selesai' => '',
        'kegiatan' => '',
        'hasil_pekerjaan' => '',
    ];

    public function mount()
    {
        $this->loadAvailableLembur();
    }

    public function loadAvailableLembur()
    {
        Lembur::where('status', 'Menunggu Pelaksanaan')
            ->whereDate('tanggal_lembur', '<=', Carbon::today())
            ->whereDoesntHave('laporan')
            ->update(['status' => 'Menunggu Laporan']);

        $user = Auth::user();
        if ($user->pegawai) {
            $this->availableLembur = Lembur::where('pegawai_id', $user->pegawai->id)
                ->where('status', 'Menunggu Laporan')
                ->whereDate('tanggal_lembur', '<=', Carbon::today())
                ->whereDoesntHave('laporan')
                ->with('suratPerintahLembur')
                ->get()
                ->map(function ($lembur) {
                    return [
                        'id' => $lembur->id,
                        'tanggal' => $lembur->tanggal_lembur,
                        'kegiatan' => $lembur->alasan_lembur,
                        'jam_mulai' => $lembur->jam_mulai,
                        'jam_selesai' => $lembur->jam_selesai,
                        'jenis_hari' => $lembur->jenis_hari,
                    ];
                })
                ->toArray();
        }
    }

    public function updatedFormLemburId()
    {
        // Find Lembur and populate jam fields
        if ($this->form['lembur_id']) {
            $lembur = Lembur::find($this->form['lembur_id']);
            if ($lembur) {
                $this->form['jam_mulai'] = $lembur->jam_mulai;
                $this->form['jam_selesai'] = $lembur->jam_selesai;
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.lembur.add-laporan-lembur');
    }

    #[On('open-add-laporan-lembur')]
    public function open() {
        // $this->resetForm();
        $this->loadAvailableLembur();
        $this->open = true;
    }

    #[On('fillFormFromLembur')]
    public function fillFormFromLembur($data)
    {
        $this->form['lembur_id'] = $data['lemburId'];
        $this->form['jam_mulai'] = $data['jamMulai'] ?? '';
        $this->form['jam_selesai'] = $data['jamSelesai'] ?? '';
        $this->form['hasil_pekerjaan'] = $data['deskripsiTugas'] ?? '';
        $this->isAutoFilled = true;
    }

    public function close() {
        $this->resetForm();
        $this->isAutoFilled = false;
        $this->open = false;
    }

    public function resetForm()
    {
        $this->form = [
            'lembur_id' => '',
            'jam_mulai' => '',
            'jam_selesai' => '',
            'kegiatan' => '',
            'hasil_pekerjaan' => '',
        ];
    }

    public function save()
    {
        $this->validate([
            'form.lembur_id' => 'required|exists:lembur,id',
            'form.jam_mulai' => 'required',
            'form.jam_selesai' => 'required',
            'form.hasil_pekerjaan' => 'required|string',
            'dokumen_laporan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $lembur = Lembur::where('id', $this->form['lembur_id'])
            ->where('pegawai_id', $user->pegawai->id ?? null)
            ->where('status', 'Menunggu Laporan')
            ->whereDate('tanggal_lembur', '<=', Carbon::today())
            ->whereDoesntHave('laporan')
            ->first();

        if (!$lembur) {
            $this->addError('form.lembur_id', 'Pengajuan lembur tidak tersedia untuk dilaporkan.');
            $this->loadAvailableLembur();
            return;
        }

        $this->validateOvertimeRules($lembur);

        $filePath = $this->dokumen_laporan
            ? $this->dokumen_laporan->store('lembur/dokumen', 'public')
            : null;

        // Create LaporanHasilLembur record
        $laporan = LaporanHasilLembur::create([
            'lembur_id' => $this->form['lembur_id'],
            'jam_mulai' => $this->form['jam_mulai'],
            'jam_selesai' => $this->form['jam_selesai'],
            'hasil_pekerjaan' => $this->form['hasil_pekerjaan'],
            'file_laporan' => $filePath,
        ]);

        WorkflowEmail::notifyLaporanSubmitted($lembur, $laporan);

        // Emit event untuk refresh data
        $this->dispatch('laporan-created');
        $this->close();
    }

    private function validateOvertimeRules(Lembur $lembur): void
    {
        $start = $this->timeToMinutes($this->form['jam_mulai']);
        $end = $this->timeToMinutes($this->form['jam_selesai']);

        if ($end <= $start) {
            throw ValidationException::withMessages([
                'form.jam_selesai' => 'Jam aktual selesai harus lebih besar dari jam aktual mulai.',
            ]);
        }

        $duration = $end - $start;

        if ($lembur->jenis_hari === 'Hari Kerja Normal') {
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

        if (in_array($lembur->jenis_hari, ['Hari Libur Mingguan', 'Hari Libur Nasional']) && $duration > 300) {
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

}
