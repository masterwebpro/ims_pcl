<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHdr extends Model
{
    use HasFactory;

    protected $table = "transfer_hdr";
    protected $primaryKey = 'id';
    protected $guarded = ['id','created_at','updated_at'];
}
