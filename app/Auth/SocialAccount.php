<?php

namespace App\Auth;

use App\Model;

class SocialAccount extends Model
{
    protected $fillable = ['user_id', 'driver', 'driver_id', 'timestamps'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
