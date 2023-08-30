<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSupplier extends Model
{
    use HasFactory;

    protected $table = 'client_supplier';
    protected $primaryKey = 'client_supplier_id';
    protected $guarded = ['client_supplier_id'];
}
