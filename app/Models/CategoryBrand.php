<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBrand extends Model
{
    use HasFactory;
    protected $table = 'category_brands';
    protected $primaryKey = 'category_brand_id';
    protected $guarded = ['category_brand_id'];
}
