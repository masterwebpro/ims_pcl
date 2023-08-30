<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WdDtlItemize extends Model
{
    use HasFactory;
    protected $table = 'wd_dtl_itemize';
    protected $primaryKey = 'wd_dtl_itemize_id';
    protected $guarded = ['wd_dtl_itemize_id'];
}
