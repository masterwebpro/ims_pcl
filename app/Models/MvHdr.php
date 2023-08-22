<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MvHdr extends Model
{
    use HasFactory;
    protected $table = 'mv_hdr';

    protected $fillable = ['ref_no','status','client_id', 'store_id','warehouse_id','remarks','created_by', 'created_at','upated_at'];

   
    public function warehouse() 
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function user_create() 
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function client() 
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
    public function store() 
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
