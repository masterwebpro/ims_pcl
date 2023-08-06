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

    public function category()
    {
        return $this->hasMany(CategoryBrand::class, 'brand_id', 'brand_id')
            ->select('category_brands.brand_id','category_brands.category_id','categories.category_name','category_brands.category_brand_id')
            ->leftJoin('categories','categories.category_id','category_brands.category_id')
            ->groupBy('categories.category_id')
            ->groupBy('category_brands.brand_id');


    }

    public function brand()
    {
        return $this->hasMany(CategoryBrand::class, 'category_id', 'category_id')
        ->select('category_brands.brand_id','category_brands.category_id','brands.brand_name','category_brands.category_brand_id')
        ->leftJoin('brands','brands.brand_id','category_brands.brand_id')
        ->groupBy('brands.brand_id')
        ->groupBy('category_brands.category_id');
    }

}
