<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'photo',
        'status',
        'stock',
        'condition',
        'description',
    ];

    // RELATION
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }
}
