<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    use HasFactory;

    protected $table = 'user_module_access';

    protected $fillable = [
        'user_id',
        'module_id',
        'permission_id',
    ];

    protected $casts = [
        "created_at" => "datetime:Y-m-d H:i:s",
        "updated_at"=>"datetime:Y-m-d H:i:s"
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'user_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(Permissions::class, 'permission_id', 'id');
    }

    public function modules()
    {
        return $this->hasMany(Modules::class, 'module_id', 'id');
    }


}
