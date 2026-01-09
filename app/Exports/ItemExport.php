<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\Sku;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // Add this
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     // Add this

class ItemExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $search;
    protected $viewType;

public function __construct($itemCode = null, $itemName = null, $viewType = 'item')
{
    $this->itemCode = $itemCode;
    $this->itemName = $itemName;
    $this->viewType = $viewType;
}



  public function query()
{
    $query = ($this->viewType === 'sku') ? Sku::query() : Item::query();

    // Apply filters
    if ($this->itemCode || ($this->itemName && $this->viewType !== 'sku')) {

        $query->where(function($q) {
            // Filter by Item_Code
            if ($this->itemCode) {
                foreach (explode(',', $this->itemCode) as $code) {
                    $q->orWhere('Item_Code', 'like', '%' . trim($code) . '%');
                }
            }

            // Filter by Item_Name ONLY for Item table
            if ($this->itemName && $this->viewType !== 'sku') {
                foreach (explode(',', $this->itemName) as $name) {
                    $q->orWhere('Item_Name', 'like', '%' . trim($name) . '%');
                }
            }
        });
    }

    return $query->orderBy('Item_Code', 'asc');
}


    /**
     * Fix for the 2.22222E+12 issue (Scientific Notation)
     */
    public function columnFormats(): array
    {
        if ($this->viewType === 'sku') {
            return [
                'G' => NumberFormat::FORMAT_TEXT, // JanCode column
            ];
        }

        return [
            'C' => NumberFormat::FORMAT_TEXT, // JanCD column
            'F' => '#,##0',                   // Format ListPrice with commas
            'G' => '#,##0',                   // Format SalePrice with commas
        ];
    }

    public function map($row): array
    {
        if ($this->viewType === 'sku') {
            return [
                $row->Item_AdminCode,
                $row->Item_Code,
                $row->Size_Name,
                $row->Color_Name,
                $row->Size_Code,
                $row->Color_Code,
                $row->JanCode,
                $row->Quantity,
            ];
        }

        return [
            $row->Item_Code,
            $row->Item_Name,
            $row->JanCD,
            $row->MakerName,
            $row->Memo,
            $row->ListPrice,
            $row->SalePrice,
        ];
    }

    public function headings(): array
    {
        if ($this->viewType === 'sku') {
            return ['Item_AdminCode', 'Item_Code', 'Size_Name', 'Color_Name', 'Size_Code', 'Color_Code', 'JanCode', 'Quantity'];
        }

        return ['Item_Code', 'Item_Name', 'JanCD', 'MakerName', 'Memo', 'ListPrice', 'SalePrice'];
    }
}