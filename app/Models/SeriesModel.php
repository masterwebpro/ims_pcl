<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeriesModel extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = ['series','trans_type','user_id', 'created_at','upated_at'];
}
