<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenuAccess extends Model
{
    use HasFactory;

    protected $table = 'user_menu_access';

    protected $fillable = [
        'user_id',
        'menu_id',
    ];

    protected $casts = [
        "created_at" => "datetime:Y-m-d H:i:s",
        "updated_at"=>"datetime:Y-m-d H:i:s"
    ];
}
