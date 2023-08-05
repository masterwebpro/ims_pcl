<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function client() 
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function store() 
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
