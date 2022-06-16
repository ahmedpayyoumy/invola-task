<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vat extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'type',
        'value'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
