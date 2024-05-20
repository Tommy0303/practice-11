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

    use Sortable;

    public $sortable = ['company_name'];
}
