<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    use HasFactory;

    protected $table = 'attributes';
    protected $primaryKey = 'attribute_id';
    protected $guarded = ['attribute_id'];

    public function category(){
        return $this->hasMany(CategoryAttribute::class, 'attribute_id', 'attribute_id')
            ->select('category_attributes.attribute_id','categories.category_id','categories.category_name')
            ->leftJoin('categories','categories.category_id','category_attributes.category_id');
    }
    public function entity(){
        return $this->hasMany(AttributeEntity::class, 'attribute_id', 'attribute_id');
    }
}
