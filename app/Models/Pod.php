<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pod extends Model
{
    use HasFactory;
    protected $table = 'pod';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(WdDtl::class, 'wd_no', 'wd_no');
    }
}
