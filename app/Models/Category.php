<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $guarded = ['category_id'];

    public function parent()
    {
        return $this->hasOne(Category::class, 'category_id', 'parent_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'category_id')->with('children');
    }

    public function brand()
    {
        return $this->hasMany(CategoryBrand::class, 'category_id', 'category_id')
            ->select('category_brands.brand_id','category_brands.category_id','brands.brand_name')
            ->leftJoin('brands','brands.brand_id','category_brands.brand_id');
    }
}
