<?php

namespace App\Livewire\Dashboard\Lembur;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Lembur;
use App\Models\LaporanHasilLembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AddLaporan extends Component
{

    public $open = false;
    public $availableLembur = [];

    public $form = [
        'lembur_id' => '',
        'jam_mulai' => '',
        'jam_selesai' => '',
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
        $this->resetForm();
        $this->loadAvailableLembur();
        $this->open = true;
    }

    public function close() {
        $this->resetForm();
        $this->open = false;
    }

    public function resetForm()
    {
        $this->form = [
            'lembur_id' => '',
            'jam_mulai' => '',
            'jam_selesai' => '',
            'hasil_pekerjaan' => '',
        ];
    }

    public function submit()
    {
        $this->validate([
            'form.lembur_id' => 'required|exists:lembur,id',
            'form.jam_mulai' => 'required',
            'form.jam_selesai' => 'required',
            'form.hasil_pekerjaan' => 'required|string',
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

        // Create LaporanHasilLembur record
        LaporanHasilLembur::create([
            'lembur_id' => $this->form['lembur_id'],
            'jam_mulai' => $this->form['jam_mulai'],
            'jam_selesai' => $this->form['jam_selesai'],
            'hasil_pekerjaan' => $this->form['hasil_pekerjaan'],
        ]);

        $lembur->update([
            'status' => 'Menunggu Verifikasi Atasan',
        ]);

        // Emit event untuk refresh data
        $this->dispatch('laporan-created');
        $this->close();
    }

}
