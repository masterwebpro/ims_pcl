<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ExportAging implements FromCollection, ShouldAutoSize, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
         return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'PRODUCT CODE',
            'PRODUCT NAME',
            'DATE RECEIVED',
            'INVENTORY',
            '30 DAYS',
            '60 DAYS',
            '90 DAYS',
            '120 DAYS',
            '150 DAYS',
            'OVER 150 DAYS'
        ];
    }
}
