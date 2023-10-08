<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Particular extends Model
{
    use HasFactory;

    protected $table = 'particular';
    protected $guarded = ['particular_id'];
    protected $primaryKey = 'particular_id';
}

