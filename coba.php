<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Services\StatusKehadiranService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class TabelRekapitulasiPresensi extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public array $unitKerja;
    public array $unitKerjaUniversitas;

    // Filter
    #[Session]
    public ?string $selectedUnitKerja = null;
    #[Session]
    public ?string $selectedPeriodeMulai = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    // Search
    #[Session]
    public $search = '';

    #[On('refresh-table-riwayat-rekapitulasi')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->unitKerja = UnitKerja::orderBy('name')
            ->pluck('name')
            ->toArray();

        $this->unitKerjaUniversitas = UnitKerja::query()
            ->whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedUnitKerja',
            'selectedPeriodeMulai',
            'selectedPeriodeSelesai',
        ])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function rekapitulasiPresensi()
    {
        // 1. Tentukan Periode (Default: Bulan Berjalan)
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        // 2. Query Pegawai dengan Eager Loading Presensi & Lembur untuk optimasi query (Hemat Query)
        $query = Pegawai::query()
            ->with([
                'presensi' => function ($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                },
                'lembur' => function ($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal_lembur', [$mulai, $selesai])
                        ->where('status', 'Disetujui');
                }
            ]);

        // Filter Unit Kerja
        if ($this->selectedUnitKerja) {
            $query->whereHas('unit_kerja', function ($q) {
                $q->where('name', $this->selectedUnitKerja);
            });
        }

        // Filter Pencarian
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

        $pegawais = $query->paginate(10);

        // 3. Transformasi Data Rekapitulasi per Pegawai
        $pegawais->getCollection()->transform(function ($pegawai) {
            $hadir = 0;
            $tidakHadir = 0;
            $cuti = 0;
            $izin = 0;
            $sakit = 0;

            $totalMenitKerja = 0;
            $totalMenitLembur = 0;

            // Map lembur by date untuk pencarian O(1)
            $lemburByDate = $pegawai->lembur->keyBy(function ($l) {
                return Carbon::parse($l->tanggal_lembur)->format('Y-m-d');
            });

            foreach ($pegawai->presensi as $presensi) {
                $tanggalStr = Carbon::parse($presensi->tanggal)->format('Y-m-d');
                $statusId = $presensi->status_kehadiran_id;

                // A. Hitung Kehadiran Berdasarkan Konstanta Service
                switch ($statusId) {
                    case StatusKehadiranService::HADIR_NORMAL:
                    case StatusKehadiranService::HADIR_KURANG_JAM:
                        $hadir++;
                        break;
                    case StatusKehadiranService::TIDAK_HADIR_KURANG_JAM:
                    case StatusKehadiranService::TIDAK_HADIR_ABSEN_1X:
                    case StatusKehadiranService::TIDAK_HADIR_TANPA_KETERANGAN:
                        $tidakHadir++;
                        break;
                    case StatusKehadiranService::IZIN:
                        $izin++;
                        break;
                    case StatusKehadiranService::SAKIT:
                        $sakit++;
                        break;
                    case StatusKehadiranService::CUTI:
                        $cuti++;
                        break;
                }

                // B. Hitung Menit Aktual Harian
                $menitKerjaHariIni = 0;
                if ($presensi->jam_masuk && $presensi->jam_keluar) {
                    $masuk = Carbon::parse($presensi->jam_masuk);
                    $keluar = Carbon::parse($presensi->jam_keluar);
                    $menitKerjaHariIni = $masuk->diffInMinutes($keluar);
                }

                // C. Logika Lembur & Jam Kerja
                $isLemburDisetujui = $lemburByDate->has($tanggalStr);
                $dataLembur = $isLemburDisetujui ? $lemburByDate->get($tanggalStr) : null;

                $isWeekend = Carbon::parse($presensi->tanggal)->isWeekend();
                $isHariLibur = $statusId == StatusKehadiranService::LEMBUR || $isWeekend || ($dataLembur && in_array($dataLembur->jenis_hari, ['Hari Libur', 'Libur Nasional']));

                // Hitung hanya jika status diakui sebagai kerja/lembur
                if (in_array($statusId, [StatusKehadiranService::HADIR_NORMAL, StatusKehadiranService::HADIR_KURANG_JAM, StatusKehadiranService::LEMBUR])) {

                    if (!$isHariLibur) {
                        // --- HARI BIASA ---
                        // Jam kerja maksimal diakui 8 jam (480 menit)
                        $menitKerjaReguler = min($menitKerjaHariIni, 480);
                        $totalMenitKerja += $menitKerjaReguler;

                        // Hitung lembur jika disetujui & jam kerja tembus 8 jam (Hadir Normal)
                        if ($isLemburDisetujui && $menitKerjaHariIni > 480) {
                            $menitKelebihan = $menitKerjaHariIni - 480;
                            // Batas maksimal lembur hari biasa = 2 jam (120 menit)
                            $totalMenitLembur += min($menitKelebihan, 120);
                        }
                    } else {
                        // --- HARI LIBUR / WEEKEND ---
                        if ($isLemburDisetujui) {
                            // Seluruh jam kerja dihitung lembur, maksimal 5 jam (300 menit)
                            $totalMenitLembur += min($menitKerjaHariIni, 300);
                        }
                    }
                }
            }

            // D. Attach properti rekap ke object pegawai untuk dipanggil di Blade
            $pegawai->rekap = [
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'cuti' => $cuti,
                'izin' => $izin,
                'sakit' => $sakit,
                'total_jam_kerja' => floor($totalMenitKerja / 60) . 'h ' . ($totalMenitKerja % 60) . 'm',
                'total_jam_lembur' => floor($totalMenitLembur / 60) . 'h ' . ($totalMenitLembur % 60) . 'm',
            ];

            return $pegawai;
        });

        return $pegawais;
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        $mulai = $this->selectedPeriodeMulai ?: now()->startOfMonth()->format('d/m/Y');
        $selesai = $this->selectedPeriodeSelesai ?: now()->endOfMonth()->format('d/m/Y');

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return "Belum ada data rekapitulasi presensi untuk periode {$mulai} - {$selesai}.";
        }

        return "Belum ada data rekapitulasi presensi untuk bulan ini (" . now()->translatedFormat('F Y') . ").";
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-rekapitulasi-presensi');
    }
}