<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlateNoList extends Model
{
    use HasFactory;
    protected $table = 'plate_no_list';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
