<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioUpload extends Model
{
    protected $fillalble=[
        'user_id',
        'audio',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
