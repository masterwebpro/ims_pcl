<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDtl extends Model
{
    use HasFactory;

    protected $table = "transfer_dtl";
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
