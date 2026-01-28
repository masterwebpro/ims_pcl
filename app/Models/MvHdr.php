<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MvHdr extends Model
{
    use HasFactory;
    protected $table = 'mv_hdr';
    protected $primaryKey = 'id';
    protected $fillable = ['ref_no','status','company_id', 'customer_id', 'store_id','warehouse_id','remarks', 'ref1_no','ref1_type','created_by', 'created_at','upated_at','start_encoding','end_encoding'];


    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function user_create()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function company()
    {

        return $this->hasOne(Client::class, 'id', 'company_id');
    }

    public function customer()
    {
        return $this->hasOne(Client::class, 'id', 'customer_id');
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
