<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PegawaiExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting, WithCustomValueBinder
{
    use Exportable;

    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query->with(['riwayatPendidikan.jenjangPendidikan', 'unit_kerja', 'jenis_pegawai', 'status_pegawai', 'memimpin_unit']);
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
            'Jenis Pegawai',
            'Status Pegawai',
            'Pendidikan Terakhir',
            'Jabatan',
            'Tanggal Bergabung',
            'Tanggal Habis Kontrak / Pensiun',
            'Status Aktif',
        ];
    }

    public function map($pegawai): array
    {
        // Get Pendidikan Terakhir
        $pendidikanTerakhir = $pegawai->riwayatPendidikan->sortByDesc(function ($riwayat) {
            return $riwayat->jenjangPendidikan->urutan ?? 0;
        })->first();
        $kodePendidikan = $pendidikanTerakhir?->jenjangPendidikan?->kode ?? '-';

        // Get Tanggal Berakhir
        $tanggalBerakhir = '-';
        if (!empty($pegawai->tanggal_habis_kontrak)) {
            $tanggalBerakhir = Carbon::parse($pegawai->tanggal_habis_kontrak)->translatedFormat('d M Y');
        } elseif (!empty($pegawai->tanggal_pensiun)) {
            $tanggalBerakhir = Carbon::parse($pegawai->tanggal_pensiun)->translatedFormat('d M Y');
        }

        // Get Jabatan
        $jabatan = $pegawai->memimpin_unit ? 'Pimpinan' : 'Pegawai';

        // Get Status Aktif
        $statusAktif = $pegawai->status === 'active' ? 'Aktif' : 'Tidak Aktif';

        return [
            $pegawai->nip ?? '-',
            $pegawai->nama ?? '-',
            $pegawai->unit_kerja?->name ?? '-',
            $pegawai->jenis_pegawai?->jenis ?? '-',
            $pegawai->status_pegawai?->status ?? '-',
            $kodePendidikan,
            $jabatan,
            $pegawai->tanggal_bergabung ? Carbon::parse($pegawai->tanggal_bergabung)->translatedFormat('d M Y') : '-',
            $tanggalBerakhir,
            $statusAktif

        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
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

        $styles = [
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