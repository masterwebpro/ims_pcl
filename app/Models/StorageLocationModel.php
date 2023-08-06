<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageLocationModel extends Model
{
    use HasFactory;

    protected $table = 'storage_locations';

    protected $fillable = ['storage_location_id', 'rack','level','location', 'reserve_to','remarks','warehouse_id', 'is_enabled', 'created_at','upated_at'];

    public function warehouse() 
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
