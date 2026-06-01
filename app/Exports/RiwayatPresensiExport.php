<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class RiwayatPresensiExport extends DefaultValueBinder implements
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

    // Inject query builder aktif dari komponen Livewire
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
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Status Kehadiran'
        ];
    }

    // Mapping memproses data per baris di dalam memory saat di-chunk, sangat efisien.
    public function map($presensi): array
    {
        return [
            $presensi->pegawai_nip,
            $presensi->pegawai->nama ?? '-',
            $presensi->pegawai->unit_kerja->name ?? '-',
            Carbon::parse($presensi->tanggal)->translatedFormat('l, d F Y'),
            $presensi->jam_masuk ? Carbon::parse($presensi->jam_masuk)->format('H:i') : '-',
            $presensi->jam_keluar ? Carbon::parse($presensi->jam_keluar)->format('H:i') : '-',
            $presensi->statusKehadiran->status ?? '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        $textColumns = ['A', 'E', 'F', 'G'];

        if (in_array($cell->getColumn(), $textColumns)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        // Mendapatkan total baris untuk styling border seluruh tabel
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $tableRange = 'A1:' . $lastColumn . $lastRow;

        $styles = [
            // Styling Header (Baris 1) - Warna Biru kustom
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE]
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E40AF'] // Tailwind Blue-800
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Styling border untuk seluruh area data
            $tableRange => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];

        return $styles;
    }
}