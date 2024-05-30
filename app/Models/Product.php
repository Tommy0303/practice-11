<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory;
    use Sortable;
    public $sortable = [
        'id', 'product_name', 'price', 'stock', 'created_at', 'updated_at' // ソート可能なカラムを指定
    ];
    public $timestamps = true;
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected $fillable = ['product_name', 'price', 'stock', 'company_id', 'img_path'];

    public function getImgPathAttribute($value)
    {
        return 'storage/' . $value;
    }
}
