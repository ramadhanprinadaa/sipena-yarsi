<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Exports\RekapitulasiPresensiExport;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Services\StatusKehadiranService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    protected function baseQuery()
    {
        $user = Auth::user();

        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        $query = Pegawai::query()
            ->select('pegawai.*')
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->with([
                'presensi' => function ($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                },
                'lembur' => function ($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal_lembur', [$mulai, $selesai])
                        ->where('status', 'Selesai')
                        ->with('laporan');
                },
                'unit_kerja:id,name' // Tambahan relasi unit_kerja untuk kemudahan di Export
            ]);

        // Scoping data
        if ($user->hasRole('SDM Universitas')) {
            $unitKerjaIds = UnitKerja::whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })->pluck('id');

            $query->whereIn('pegawai.unit_kerja_id', $unitKerjaIds);
            // ->where('pegawai.id', '!=', $user->pegawai?->id);
        } elseif ($user->hasRole('Pimpinan')) {
            $unit_id = $user->pegawai?->memimpin_unit?->id;

            if (!$unit_id) {
                $query->whereNull('pegawai.id');
            } else {
                $query->where('pegawai.unit_kerja_id', $unit_id);
                // ->where('pegawai.id', '!=', $user->pegawai?->id);
            }
        }

        $query->orderBy('unit_kerja.name', 'asc')->orderBy('pegawai.nama', 'asc');

        if ($this->selectedUnitKerja) {
            $query->where('unit_kerja.name', $this->selectedUnitKerja);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pegawai.nama', 'like', '%' . $this->search . '%')
                    ->orWhere('pegawai.nip', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    #[Computed]
    public function rekapitulasiPresensi()
    {
        // Inisialisasi base query rekapitulasi presensi
        $pegawais = $this->baseQuery()->paginate(10);

        // Transformasi data rekapitulasi per pegawai
        $pegawais->getCollection()->transform(function ($pegawai) {
            // Inisialisasi variabel data rekapitulasi
            $hadir = $tidakHadir = $lembur = $cuti = $izin = $sakit = $totalMenitKerja = $totalMenitLembur = 0;

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
                $pmStart = null;
                $pmEnd = null;

                if ($presensi->jam_masuk && $presensi->jam_keluar) {
                    $masuk = Carbon::parse($presensi->jam_masuk);
                    $keluar = Carbon::parse($presensi->jam_keluar);
                    $menitKerjaHariIni = $masuk->diffInMinutes($keluar);

                    // Variabel untuk menghitung irisan (overlap) dengan jam lembur
                    $pmStart = Carbon::parse($tanggalStr . ' ' . $masuk->format('H:i:s'));
                    $pmEnd = Carbon::parse($tanggalStr . ' ' . $keluar->format('H:i:s'));
                    if ($pmEnd->lessThan($pmStart)) $pmEnd->addDay();
                }

                // C. Logika Lembur & Jam Kerja
                $isLemburSelesai = $lemburByDate->has($tanggalStr);
                $dataLembur = $isLemburSelesai ? $lemburByDate->get($tanggalStr) : null;

                $durasiLemburMenit = 0;
                $totalMenitEfektif = $menitKerjaHariIni; // Secara default adalah total presensi

                if ($isLemburSelesai) {
                    // Ambil jam lembur dari Laporan (jika ada), jika tidak gunakan jam di pengajuan lembur
                    $jamMulaiLembur = $dataLembur->laporan ? $dataLembur->laporan->jam_mulai : $dataLembur->jam_mulai;
                    $jamSelesaiLembur = $dataLembur->laporan ? $dataLembur->laporan->jam_selesai : $dataLembur->jam_selesai;

                    $lStart = Carbon::parse($tanggalStr . ' ' . $jamMulaiLembur);
                    $lEnd = Carbon::parse($tanggalStr . ' ' . $jamSelesaiLembur);
                    if ($lEnd->lessThan($lStart)) $lEnd->addDay(); // Handle lembur lintas hari

                    $durasiLemburMenit = $lStart->diffInMinutes($lEnd);

                    // Kalkulasi Total Menit Efektif untuk mencegah perhitungan ganda
                    if ($pmStart && $pmEnd) {
                        $overlapStart = $pmStart->max($lStart);
                        $overlapEnd = $pmEnd->min($lEnd);

                        $overlapMinutes = 0;
                        if ($overlapStart->lessThan($overlapEnd)) {
                            $overlapMinutes = $overlapStart->diffInMinutes($overlapEnd);
                        }

                        // Total waktu gabungan = Waktu Presensi + Waktu Laporan Lembur - Irisan Waktu
                        $totalMenitEfektif = $menitKerjaHariIni + $durasiLemburMenit - $overlapMinutes;
                    } else {
                        // Jika presensi kosong (misal disetujui hadir manual), pakai durasi laporan
                        $totalMenitEfektif = $durasiLemburMenit;
                    }
                }

                $isWeekend = Carbon::parse($presensi->tanggal)->isWeekend();
                $isHariLibur = $statusId == StatusKehadiranService::LEMBUR || $isWeekend || ($dataLembur && in_array($dataLembur->jenis_hari, ['Hari Libur', 'Libur Nasional']));

                // Set variable untuk menghitung total frekuensi libur
                $menitLemburValidHariIni = 0;

                // Hitung hanya jika status diakui sebagai kerja/lembur
                if (in_array($statusId, [
                    StatusKehadiranService::HADIR_NORMAL,
                    StatusKehadiranService::HADIR_KURANG_JAM,
                    StatusKehadiranService::LEMBUR
                ])) {

                    if (!$isHariLibur) {
                        // --- HARI BIASA ---
                        // Jam kerja maksimal diakui 8 jam (480 menit)
                        $menitKerjaReguler = min($menitKerjaHariIni, 480);
                        $totalMenitKerja += $menitKerjaReguler;

                        // PERBAIKAN: Hitung lembur jika status selesai & Total Menit Efektif > 8 jam
                        if ($isLemburSelesai && $totalMenitEfektif > 480) {
                            $kelebihanMenit = $totalMenitEfektif - 480;

                            // Batas maksimal lembur hari biasa = 2 jam (120 menit).
                            $menitLemburValidHariIni = min($kelebihanMenit, 120);
                            $totalMenitLembur += $menitLemburValidHariIni;
                        }
                    } else {
                        // --- HARI LIBUR / WEEKEND ---
                        if ($isLemburSelesai) {
                            // Seluruh jam kerja dihitung lembur, maksimal 5 jam (300 menit)
                            $menitLemburValidHariIni = min($menitKerjaHariIni, 300);
                            $totalMenitLembur += $menitLemburValidHariIni;
                        }
                    }
                }
                // Jika pada hari tersebut pegawai diakui mendapatkan menit lembur > 0,
                // maka frekuensi / jumlah lemburnya bertambah 1 kali.
                if ($menitLemburValidHariIni > 0) {
                    $lembur++;
                }
            }
            // D. Attach properti rekap ke object pegawai
            $pegawai->rekap = [
                'hadir'            => $hadir,
                'tidak_hadir'      => $tidakHadir,
                'lembur'           => $lembur,
                'cuti'             => $cuti,
                'izin'             => $izin,
                'sakit'            => $sakit,
                'total_jam_kerja'  => floor($totalMenitKerja / 60) . 'h ' . ($totalMenitKerja % 60) . 'm',
                'total_jam_lembur' => floor($totalMenitLembur / 60) . 'h ' . ($totalMenitLembur % 60) . 'm',
            ];

            return $pegawai;
        });

        return $pegawais;
    }

    #[Computed]
    public function infoPeriodeAktif(): string
    {
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)
            : now()->endOfMonth();

        $formatMulai = $mulai->translatedFormat('d F Y');
        $formatSelesai = $selesai->translatedFormat('d F Y');

        // Jika filter kosong, beri keterangan tambahan "Bulan Berjalan"
        if (!$this->selectedPeriodeMulai && !$this->selectedPeriodeSelesai) {
            return "Bulan Berjalan ({$formatMulai} — {$formatSelesai})";
        }

        return "{$formatMulai} s/d {$formatSelesai}";
    }

    #[Computed] // Untuk mengecek apakah data presensi pada periode terpilih sudah ada
    public function hasPresensiData(): bool
    {
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        return $this->baseQuery()
            ->without(['presensi', 'lembur', 'unit_kerja'])
            ->whereHas('presensi', function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal', [$mulai, $selesai]);
            })
            ->exists();
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        // 1. Cek jika pegawainya yang kosong
        if (!$this->baseQuery()->exists()) {
            if ($this->search) {
                return "Pegawai dengan kata pencarian '{$this->search}' tidak ditemukan.";
            }
            return "Tidak ada data pegawai pada filter atau unit kerja yang dipilih.";
        }

        // 2. Jika pegawai ada, berarti presensinya yang kosong
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->translatedFormat('d F Y')
            : now()->startOfMonth()->translatedFormat('d F Y');
        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->translatedFormat('d F Y')
            : now()->endOfMonth()->translatedFormat('d F Y');

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return "Belum ada data rekapitulasi presensi yang terekam untuk periode: {$mulai} s/d {$selesai}.";
        }

        return "Belum ada data rekapitulasi presensi yang terekam untuk bulan " . now()->translatedFormat('F Y') . ".";
    }

    #[Computed]
    public function exportPreviewData()
    {
        $isPegawaiEmpty = !$this->baseQuery()->exists();
        $hasPresensi = $this->hasPresensiData;

        // Export dianggap KOSONG (tombol mati) jika pegawai tidak ada atau presensi tidak ada
        $isEmpty = $isPegawaiEmpty || !$hasPresensi;

        $singleEmployeeName = null;
        if (!$isPegawaiEmpty && !empty($this->search)) {
            $uniqueNips = $this->baseQuery()
                ->reorder() // Mencegah error SQL Strict Mode
                ->select('pegawai.nip')
                ->distinct()
                ->limit(2)
                ->pluck('pegawai.nip');

            if ($uniqueNips->count() === 1) {
                $pegawai = Pegawai::where('nip', $uniqueNips->first())->first();
                $singleEmployeeName = $pegawai ? $pegawai->nama : 'NIP. ' . $uniqueNips->first();
            }
        }

        return [
            'isEmpty'        => $isEmpty,
            'periode'        => $this->infoPeriodeAktif,
            'unit'           => $this->selectedUnitKerja ?? 'Semua Unit Kerja',
            'singleEmployee' => $singleEmployeeName,
        ];
    }

    public function exportData()
    {
        if (!$this->baseQuery()->exists() || !$this->hasPresensiData) {
            return;
        }

        $query = $this->baseQuery();
        $preview = $this->exportPreviewData;

        $fileNameParts = ['Rekapitulasi_Presensi'];

        if (!empty($preview['singleEmployee'])) {
            $fileNameParts[] = str_replace(' ', '_', $preview['singleEmployee']);
        }
        if ($this->selectedUnitKerja) {
            $fileNameParts[] = str_replace(' ', '_', $this->selectedUnitKerja);
        }

        $periodeAman = str_replace([' ', '(', ')', '/', '—'], ['_', '', '', '-', '-'], $preview['periode']);
        $fileNameParts[] = $periodeAman;

        $fileName = implode('_', $fileNameParts) . '.xlsx';

        return (new RekapitulasiPresensiExport($query))->download($fileName);
    }

    public function openExportPreview(): void
    {
        $this->dispatch('open-export');
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-rekapitulasi-presensi');
    }
}
