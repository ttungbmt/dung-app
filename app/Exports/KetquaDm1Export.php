<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KetquaDm1Export implements FromArray, WithHeadings
{
    protected $items;

    public function headings(): array
    {
        return ['STT', 'Danh mục KT', 'Danh mục DC', 'Giá', 'Bệnh viện'];
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
