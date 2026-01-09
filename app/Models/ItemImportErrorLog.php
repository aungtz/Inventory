<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImportErrorLog extends Model
{
    protected $table = 'item_import_error_logs';

    protected $fillable = [
        'ImportLog_ID',
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
        'Error_Msg',
    ];

    public $timestamps = false;

   public function log()
    {
        return $this->belongsTo(ItemImportLog::class, 'ImportLog_ID', 'ImportLog_ID');
    }
}

