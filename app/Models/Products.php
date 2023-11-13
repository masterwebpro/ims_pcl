<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $guarded = ['product_id'];

    public function category_brand()
    {
        return $this->hasOne(CategoryBrand::class, 'category_brand_id', 'category_brand_id')
            ->select('category_brands.category_brand_id','category_brands.brand_id','category_brands.category_id','categories.category_name','brands.brand_name')
            ->leftJoin('categories','categories.category_id','category_brands.category_id')
            ->leftJoin('brands','brands.brand_id','category_brands.brand_id');
    }

    public function unit()
    {
        return $this->hasOne(ProductUom::class, 'product_id', 'product_id')
        ->select('product_uom.*','uom.code','uom.uom_desc')
        ->leftJoin('uom','uom.uom_id','product_uom.uom_id');
    }
}
