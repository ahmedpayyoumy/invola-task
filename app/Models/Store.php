<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'phone',
        'address',
        'VAT',
        'shipping'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isVatEnabled()
    {
        return ($this['VAT'] == 1);
    }

    public function isShippingEnabled()
    {
        return ($this['shipping'] == 1);
    }

    public function vat()
    {
        return $this->hasOne(Vat::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }
}
