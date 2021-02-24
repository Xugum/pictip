<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Streamers extends Model
{
    use SoftDeletes;

    protected $table = 'streamers';
    
    protected $fillable = [
        'user_id', 'token', 'picpay_token', 'seller_token', 'se_jwt'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
