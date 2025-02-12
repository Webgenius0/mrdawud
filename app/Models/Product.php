<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $guarded=[];

  //protected $table = 'products';  // Ensure this is correct if the table name is not 'products'
  protected $primaryKey = 'id';
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
