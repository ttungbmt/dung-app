<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KetquaDmExport implements FromArray, WithHeadings
{
    protected $items;

    public function headings(): array
    {
        return [
            '#',
            'Danh mục KT',
            'BV',
            'Giá',
            'BV',
            'Giá',
            'BV',
            'Giá',
        ];
    }

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function array(): array
    {
        return $this->items;
    }
}
