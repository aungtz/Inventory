<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\Sku;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // Add this
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     // Add this
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class ItemExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting , WithCustomCsvSettings
{
    protected $search;
    protected $viewType;

    protected $isCsv;

public function __construct(
    $itemCode = null,
    $itemName = null,
    $viewType = 'item',
    $janCode = null,
    $adminCode = null,
    $isCsv = false
) {
    $this->itemCode  = $itemCode;
    $this->itemName  = $itemName;
    $this->viewType  = $viewType;
    $this->janCode   = $janCode;
    $this->adminCode = $adminCode;
    $this->isCsv = $isCsv;
}


public function getCsvSettings(): array
{
    return [
        'use_bom' => true,   // ⭐ THIS FIXES JAPANESE
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => PHP_EOL,
    ];
}



  public function query()
{
    $query = ($this->viewType === 'sku')
        ? Sku::query()
        : Item::query();

    if ($this->itemCode || ($this->itemName && $this->viewType !== 'sku')) {
        $query->where(function ($q) {

            if ($this->itemCode) {
                foreach (explode(',', $this->itemCode) as $code) {
                    $q->orWhere('Item_Code', 'like', '%' . trim($code) . '%');
                }
            }

            if ($this->itemName && $this->viewType !== 'sku') {
                foreach (explode(',', $this->itemName) as $name) {
                    $q->orWhere('Item_Name', 'like', '%' . trim($name) . '%');
                }
            }
        });
    }

    /* ================================
       ✅ ADD SKU-ONLY EXPORT FILTERS
       ================================ */
    if ($this->viewType === 'sku' && $this->janCode) {
        $query->where('JanCode', 'like', '%' . trim($this->janCode) . '%');
    }

   if ($this->viewType === 'sku' && $this->adminCode) {
    $query->where(function ($q) {
        foreach (explode(',', $this->adminCode) as $code) {
            $q->orWhere(
                'Item_AdminCode',
                'like',
                '%' . trim($code) . '%'
            );
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
            $this->text($row->Item_AdminCode),
            $this->text($row->Item_Code),
            $row->Size_Name,
            $row->Color_Name,
            $this->text($row->Size_Code),
            $this->text($row->Color_Code),
            $this->text($row->JanCode),
            $row->Quantity,
        ];
    }

    return [
        $this->text($row->Item_Code),
        $this->text($row->Item_Name),
        $this->text($row->JanCD),
        $row->MakerName,
        $row->Memo,
        $row->ListPrice,
        $row->SalePrice,
    ];
}

private function text($value)
{
    if ($this->isCsv) {
        return '="' . $value . '"'; // CSV-safe
    }

    return $value; // Excel-safe
}


    public function headings(): array
    {
        if ($this->viewType === 'sku') {
            return ['Item_AdminCode', 'Item_Code', 'Size_Name', 'Color_Name', 'Size_Code', 'Color_Code', 'JanCode', 'Quantity'];
        }

        return ['Item_Code', 'Item_Name', 'JanCD', 'MakerName', 'Memo', 'ListPrice', 'SalePrice'];
    }
}
//09 -Feb-2026
