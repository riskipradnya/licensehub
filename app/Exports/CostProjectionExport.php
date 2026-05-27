<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class CostProjectionExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents
{
    protected $licenses;
    protected $startDate;
    protected $endDate;
    protected $rowNumber = 1;

    public function __construct(Collection $licenses, $startDate, $endDate)
    {
        $this->licenses = $licenses;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->licenses;
    }

    public function headings(): array
    {
        return [
            'No',
            'License Name',
            'Vendor',
            'Target/Expiry Date',
            'Projected Cost (IDR)'
        ];
    }

    public function map($license): array
    {
        return [
            $this->rowNumber++,
            $license->name,
            $license->vendor->name ?? '-',
            \Carbon\Carbon::parse($license->expiry_date)->format('d/m/Y'),
            $license->cost
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '"Rp"#,##0_-', // Format Rupiah (angka bisa di-SUM)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $highestRow = $event->sheet->getHighestRow();
                $totalRow = $highestRow + 1;
                
                // Tulis label dan Merge dari A sampai D
                $event->sheet->setCellValue('A' . $totalRow, 'GRAND TOTAL');
                $event->sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                
                // Suntikkan rumus =SUM native Excel pada kolom E
                $event->sheet->setCellValue('E' . $totalRow, '=SUM(E2:E' . $highestRow . ')');
                
                // Terapkan Corporate Styling pada baris Grand Total
                $event->sheet->getDelegate()->getStyle('A' . $totalRow . ':E' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0'], // Abu-abu muda
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        ],
                    ],
                ]);
            },
        ];
    }
}
