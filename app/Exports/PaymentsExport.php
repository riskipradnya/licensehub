<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PaymentsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $payments;

    public function __construct(Collection $payments)
    {
        $this->payments = $payments;
    }

    public function view(): View
    {
        return view('exports.payments-pdf', [
            'payments' => $this->payments
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
