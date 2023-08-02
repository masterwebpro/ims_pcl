<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcvDtlItemize extends Model
{
    use HasFactory;

    protected $table = 'rcv_dtl_itemize';

    protected $fillable = [
        'rcv_dtl_id',
        'attribute_id',
        'item_value',
        'is_available',
        'updated_at',
        'created_at'
    ];
}
