<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    protected $table = 'M_ItemImage';

    public $timestamps = false;
    

    protected $fillable = [
        'Item_Code',
        'Image_Name',
        'Image_Serial',
        'slot',
        'CreatedDate',
        'UpdatedDate',
    ];


    public function item()
    {
        return $this->belongsTo(Item::class, 'Item_Code', 'Item_Code');
    }
}

