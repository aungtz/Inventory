<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ItemImportDataLog extends Model
{
    protected $table = 'item_import_data_logs';
protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'ImportLog_ID',
        'Item_AdminCode',
        'Item_Code',
        'Item_Name',
        'JanCD',
        'MakerName',
        'Memo',
        'ListPrice',
        'SalePrice',
        'Size_Name',
        'Color_Name',
        'Size_Code',
        'Color_Code',
        'JanCode',
        'Quantity',
       
    ];
    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

    public $timestamps = true;
    public function log()
    {
        return $this->belongsTo(ItemImportLog::class, 'ImportLog_ID', 'ImportLog_ID');
    }
}

