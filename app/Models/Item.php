<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'M_Item';
    protected $primaryKey = 'Item_Code';
    public $incrementing = false; // if Item_Code is not an auto-increment
    protected $keyType = 'string';
    public $timestamps = false; // disable default created_at/updated_at

protected $fillable = [
    'Item_Code', 'Item_Name', 'JanCD', 'MakerName', 'Memo',
     'ListPrice',  'SalePrice',
    'CreatedBy', 'UpdatedBy'
];


    public function skus()
    {
        return $this->hasMany(Sku::class, 'Item_Code', 'Item_Code');
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class, 'Item_Code', 'Item_Code');
    }
}
