<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QueueExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $queues;
    protected $stats;

    public function __construct($queues, $stats)
    {
        $this->queues = $queues;
        $this->stats = $stats;
    }

    public function view(): View
    {
        return view('Pages.AdminInstansi.export_table', [
            'queues' => $this->queues,
            'stats' => $this->stats
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
