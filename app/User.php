<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'user_id', 'username', 'email', 'profile_image', 'token'
    ];
    
    protected $hidden = [
        'email', 'token'
    ];

    public function payments()
    {
        return $this->belongsTo(Payments::class, 'id', 'user_id');
    }

    public function received()
    {
        return $this->belongsTo(Payments::class, 'id', 'to_user');
    }

    public function stream()
    {
        return $this->belongsTo(Streamers::class, 'id', 'user_id');
    }
}
