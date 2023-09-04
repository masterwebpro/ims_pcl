<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchDtl extends Model
{
    use HasFactory;

    protected $table = "dispatch_dtl";
    protected $primaryKey = ['id','created_at','updated_at'];
}
