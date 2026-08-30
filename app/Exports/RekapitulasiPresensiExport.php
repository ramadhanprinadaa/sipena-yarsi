<?php

namespace App\Exports;

use App\Services\StatusKehadiranService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapitulasiPresensiExport extends DefaultValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithColumnFormatting,
    WithCustomValueBinder
{
    use Exportable;

    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Pegawai',
            'Unit Kerja',
            'Hadir',
            'Tidak Hadir',
            'Lembur',
            'Cuti',
            'Izin',
            'Sakit',
            'Total Jam Kerja',
            'Total Jam Lembur'
        ];
    }

    // Melakukan perhitungan agregasi dinamis saat proses mapping export
    public function map($pegawai): array
    {
        $hadir = $tidakHadir = $lembur = $cuti = $izin = $sakit = $totalMenitKerja = $totalMenitLembur = 0;

        $lemburByDate = $pegawai->lembur->keyBy(fn($l) => Carbon::parse($l->tanggal_lembur)->format('Y-m-d'));

        foreach ($pegawai->presensi as $presensi) {
            $tanggalStr = Carbon::parse($presensi->tanggal)->format('Y-m-d');
            $statusId = $presensi->status_kehadiran_id;

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

            $menitKerjaHariIni = 0;
            if ($presensi->jam_masuk && $presensi->jam_keluar) {
                $menitKerjaHariIni = Carbon::parse($presensi->jam_masuk)->diffInMinutes(Carbon::parse($presensi->jam_keluar));
            }

            $isLemburDisetujui = $lemburByDate->has($tanggalStr);
            $dataLembur = $isLemburDisetujui ? $lemburByDate->get($tanggalStr) : null;
            $isHariLibur = $statusId == StatusKehadiranService::LEMBUR || Carbon::parse($presensi->tanggal)->isWeekend() || ($dataLembur && in_array($dataLembur->jenis_hari, ['Hari Libur', 'Libur Nasional']));
            $menitLemburValidHariIni = 0;

            if (in_array($statusId, [StatusKehadiranService::HADIR_NORMAL, StatusKehadiranService::HADIR_KURANG_JAM, StatusKehadiranService::LEMBUR])) {
                if (!$isHariLibur) {
                    $totalMenitKerja += min($menitKerjaHariIni, 480);
                    if ($isLemburDisetujui && $menitKerjaHariIni > 480) {
                        $menitLemburValidHariIni = min(($menitKerjaHariIni - 480), 120);
                        $totalMenitLembur += $menitLemburValidHariIni;
                    }
                } else {
                    if ($isLemburDisetujui) {
                        $menitLemburValidHariIni = min($menitKerjaHariIni, 300);
                        $totalMenitLembur += $menitLemburValidHariIni;
                    }
                }
            }
            if ($menitLemburValidHariIni > 0) $lembur++;
        }

        return [
            $pegawai->nip,
            $pegawai->nama ?? '-',
            $pegawai->unit_kerja->name ?? '-',
            $hadir === 0 ? '-' : $hadir,
            $tidakHadir === 0 ? '-' : $tidakHadir,
            $lembur === 0 ? '-' : $lembur,
            $cuti === 0 ? '-' : $cuti,
            $izin === 0 ? '-' : $izin,
            $sakit === 0 ? '-' : $sakit,
            $totalMenitKerja === 0 ? '-' : floor($totalMenitKerja / 60) . 'h ' . ($totalMenitKerja % 60) . 'm',
            $totalMenitLembur === 0 ? '-' : floor($totalMenitLembur / 60) . 'h ' . ($totalMenitLembur % 60) . 'm',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Agar NIP menjadi format text dan angka 0 di depan tidak hilang
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        $textColumns = ['A'];

        if (in_array($cell->getColumn(), $textColumns)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $tableRange = 'A1:' . $lastColumn . $lastRow;

        return [
            // Custom Styling Header (Baris 1) - Warna Hijau/Emerald kustom
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE]
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF059669'] // Tailwind Emerald-600
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            $tableRange => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];
    }
}