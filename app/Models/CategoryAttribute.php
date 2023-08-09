<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    use HasFactory;
    protected $table = 'category_attributes';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $fillable = [
        'attribute_id',
        'category_id',
        'sort'
    ];

    protected $casts = [
        "created_at" => "datetime:Y-m-d H:i",
        "updated_at"=>"datetime:Y-m-d H:i"
    ];
}
