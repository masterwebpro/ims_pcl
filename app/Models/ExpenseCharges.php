<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCharges extends Model
{
    use HasFactory;
    protected $table = 'expense_charges';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
