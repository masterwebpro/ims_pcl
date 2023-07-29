<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UOM extends Model
{
    use HasFactory;

    protected $table = 'uom';
    protected $primaryKey = 'uom_id';
    protected $guarded = ['uom_id'];
}
