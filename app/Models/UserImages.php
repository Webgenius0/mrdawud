<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserImages extends Model
{
    protected $fillable = [
        'user_id',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favouriteTeachers()
    {
        return $this->belongsTo(FavouriteTeacher::class,'teacher_id');
    }
    
    
}
