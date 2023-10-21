<?php

namespace App\Exports;

use App\Models\DispatchDtl;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportOutboundMonitoring implements FromCollection, ShouldAutoSize, WithHeadings
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
            'WEEK NO',
            'DATE DISPATCH',
            'TRUCK TYPE',
            'TRUCKING',
            'DR NO',
            'SEAL NO',
            'PLATE NO',
            'DRIVER/HELPER',
            'PO NO',
            'ORDER NO',
            'INV NO',
            'SUPPLIER',
            'CATEGORY',
            'MATERIAL NO',
            'MATERIAL DESCRIPTION',
            'BATCH CODE',
            'PACK SIZE',
            'NUMBER OF (Ctn)',
            'QUANTITY (in PCS)',
            'UOM',
            'MFG. DATE',
            'EXPIRY DATE',
            'MATERIAL DOC NO',
            'STATUS',
            'REMARKS',
            'REASON OF UNENCODED',
            'TIME START PICKING',
            'END OF PICKING',
            'ACTUAL TRUCK ARRIVAL',
            'TIME START LOADING',
            'END OF LOADING',
            'ACTUAL TIME OUT',
            'LOADING PERFORMANCE',
            'DWELL TIME',
            'PALLET USE',
            'ENCODER',
            'CHECKER'
        ];
    }

}
