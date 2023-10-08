<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDtl extends Model
{
    use HasFactory;
    protected $table = 'expense_dtl';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
