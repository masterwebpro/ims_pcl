<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    use HasFactory;

    protected $table = 'modules';

    protected $fillable = [
        'module_name',
        'display_name',
    ];

    protected $casts = [
        "created_at" => "datetime:Y-m-d H:i:s",
        "updated_at"=>"datetime:Y-m-d H:i:s"
    ];
}
