<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'menu_id',
        'submenu_name',
        'icon',
        'is_enabled',
        'sort',
        'url',
    ];


    public function parent()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'menu_id', 'id')->with('children');
    }

}
