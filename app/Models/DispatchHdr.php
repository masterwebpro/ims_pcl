<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchHdr extends Model
{
    use HasFactory;
    protected $table = "dispatch_hdr";
    protected $primaryKey = ['id','created_at','updated_at'];
}
