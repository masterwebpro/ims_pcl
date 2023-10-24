<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHdr extends Model
{
    use HasFactory;

    protected $table = "transfer_hdr";
    protected $primaryKey = 'id';
    protected $guarded = ['id','created_at','updated_at'];

    public function user_create() 
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function company() 
    {
        return $this->hasOne(Client::class, 'id', 'source_company_id');
    }

    public function store() 
    {
        return $this->hasOne(Store::class, 'id', 'source_store_id');
    }
}
