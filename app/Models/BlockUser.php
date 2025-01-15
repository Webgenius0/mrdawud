<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockUser extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'blocked_user_id',
    ];

    /**
     * Get Blocked User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blocked_user(){
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}
