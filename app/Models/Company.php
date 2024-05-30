<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name', 
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    use Sortable;

    public $sortable = ['company_name'];
}
