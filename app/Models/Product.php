<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
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
