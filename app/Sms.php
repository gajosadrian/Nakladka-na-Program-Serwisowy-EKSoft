<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $casts = [
        'phones' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
