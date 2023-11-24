<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trucker extends Model
{
    use HasFactory;
    protected $table = 'trucker_list';
    protected $guarded = ['id'];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];
}
