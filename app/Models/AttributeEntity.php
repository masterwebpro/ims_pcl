<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeEntity extends Model
{
    use HasFactory;
    protected $table = 'attribute_entities';
    protected $primaryKey = 'attribute_entity_id';
    protected $guarded = ['attribute_entity_id'];
}
