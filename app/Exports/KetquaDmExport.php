<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KetquaDmExport implements FromArray, WithHeadings
{
    protected $items;

    public function headings(): array
    {
        $limit = request()->input('limit', 3);
        return collect(range(1, $limit))->map(fn($v, $k) => ['Giá_'.$v, 'BV_'.$v])->prepend(['#', 'Danh mục KT'])->collapse()->all();
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
