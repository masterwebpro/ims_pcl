<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseHdr extends Model
{
    use HasFactory;
    protected $table = 'expense_hdr';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(ExpenseDtl::class, 'expense_no', 'expense_no');
    }

    public function charges()
    {
        return $this->hasMany(ExpenseCharges::class, 'expense_no', 'expense_no');
    }
}
