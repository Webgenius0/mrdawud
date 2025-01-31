<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{

    use HasFactory;
    
    // Use $fillable to define which fields are mass-assignable
    protected $fillable = [
        'user_id',
        'order_id',
        'name',
        'address',
        'phone_number',
        'city',
        'state',
        'zip_code',
        'billing_address_id',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
