<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';
    
    protected $fillable = [
        'user_id', 'to_user', 'amount', 'message', 'order_id', 'status', 'authorization_id', 'payment_url', 'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function streamer()
    {
        return $this->belongsTo(Streamers::class, 'to_user', 'user_id');
    }
}
