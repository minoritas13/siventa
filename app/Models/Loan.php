<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $fillable = [
        'user_id',
        'loan_date',
        'return_date',
        'status',
        'note',
    ];

    // RELATION
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }
}
