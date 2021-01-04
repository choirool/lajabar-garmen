<?php

namespace App\Exports;

use App\Models\User;
use App\Models\CustomerItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerProductExport implements
    FromCollection,
    WithHeadings
{
    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->getData()->map(function ($item) {
            return [
                'item_name' => $item->item_name,
                'unit' => $item->unit,
                'category_name' => $item->category_name,
                'material_name' => $item->material_name,
                'color_name' => $item->color_name,
                'price' => $item->prices->first()->price,
                'special_price' => $item->prices->first()->special_price,
                'printing' => $item->screen_printing ? 'Yes' : 'No',
                'note' => $item->note,
                'special_note' => $item->special_note,
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Customer name', $this->customer->name],
            ['Phone', $this->customer->phone],
            ['Email', $this->customer->email],
            [''],
            [''],
            ['Item name', 'Unit', 'Type', 'Material', 'Color', 'Price', 'Special Price', 'Sablon', 'Note', 'Special Note'],
        ];
    }

    protected function getData()
    {
        return CustomerItem::query()
            ->select(
                'customer_items.id',
                'customer_items.screen_printing',
                'customer_items.note',
                'customer_items.special_note',
                'items.name as item_name',
                'items.unit',
                'categories.name as category_name',
                'materials.name as material_name',
                'colors.name as color_name',
            )
            ->join('items', 'items.id', '=', 'customer_items.item_id')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->join('materials', 'materials.id', '=', 'items.material_id')
            ->join('colors', 'colors.id', '=', 'customer_items.color_id')
            ->where('customer_id', $this->customer->id)
            ->with('prices')
            ->get();
    }
}
