<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterdataModel extends Model
{
    use HasFactory;

    protected $table = 'masterdata';
    protected $guarded = ['id','updated_at','created_at'];
}
