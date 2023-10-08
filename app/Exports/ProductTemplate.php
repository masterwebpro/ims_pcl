<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductTemplate implements WithMultipleSheets, WithTitle
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
        return new Collection($this->data);
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new HeaderSheet($this->data['header'],'Header');
        $sheets[] = new SupplierSheet($this->data['supplier'],'Supplier List');
        $sheets[] = new CategoryBrandSheet($this->data['category_brand'],'Category Brand List');
        $sheets[] = new UnitSheet($this->data['unit'],'Unit of Measurement');

        return $sheets;
    }

    public function title(): string
    {
        return 'Product Template';
    }

}
