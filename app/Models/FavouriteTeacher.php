<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavouriteTeacher extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function favouriteTeachers()
    {
        return $this->belongsTo(FavouriteTeacher::class,'teacher_id');
    }
}
