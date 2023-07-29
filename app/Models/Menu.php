<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'menu_name',
        'icon',
        'is_enabled',
        'sort',
        'url',
    ];


    public function parent()
    {
        return $this->hasOne(Menu::class, 'id', 'parent_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')->with('children');
    }
}
