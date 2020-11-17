<?php

namespace App\Http\Responses\Order\Prebilling\Excel;

use App\Models\Size;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrebillingExport implements FromView, WithStyles
{
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function view(): View
    {
        return view('excels.prebilling', [
            'order' => $this->order,
            'sizes' => Size::all(),
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'font' => ['bold' => true]
            ],
            2 => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
