<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUom extends Model
{
    use HasFactory;

    protected $table = 'product_uom';
    protected $primaryKey = 'product_uom';
    protected $guarded = ['product_uom'];
}
