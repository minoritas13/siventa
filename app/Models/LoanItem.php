<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanItem extends Model
{
    use HasFactory;

    protected $table = 'loan_items';

    protected $fillable = [
        'loan_id',
        'item_id',
        'quantity',
    ];

    // RELATION
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
