<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ItemImportLog extends Model
{
    protected $table = 'item_import_logs';
    protected $primaryKey = 'ImportLog_ID';

    protected $fillable = [
        'Import_Type',
        'Record_Count',
        'Error_Count',
        'Imported_By',
        'Imported_Date',
    ];
protected $casts = [
    'Imported_Date' => 'datetime',
];
    public $timestamps = false;

    public function dataRows()
    {
        return $this->hasMany(ItemImportDataLog::class, 'ImportLog_ID', 'ImportLog_ID');
    }

    public function errorRows()
    {
        return $this->hasMany(ItemImportErrorLog::class, 'ImportLog_ID', 'ImportLog_ID');
    }
}
