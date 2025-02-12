<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remainder extends Model
{
    protected $guarded=[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function audio()
    {
        return $this->belongsTo(AudioUpload::class,'audio','id');
    }
}
