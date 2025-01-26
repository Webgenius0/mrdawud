<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'phone_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
