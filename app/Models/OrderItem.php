<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded=[];

    public function order_items()
{
    return $this->hasMany(OrderItem::class);
}

public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
