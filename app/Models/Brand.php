<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    protected $guarded = ['brand_id'];

    public function category()
    {
        return $this->hasMany(CategoryBrand::class, 'brand_id', 'brand_id')
            ->select('category_brands.brand_id','category_brands.category_id','categories.category_name')
            ->rightJoin('categories','categories.category_id','category_brands.category_id');
    }
}
