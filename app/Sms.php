<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $connection = 'mysql';
    protected $table = 'sms';
    protected $appends = [
        'message_formatted', 'sms_amount', 'date'
    ];
    protected $casts = [
        'phones' => 'array',
        'auto' => 'boolean',
    ];

    public const SMS_LENGTH = 160;
    public const FOOTER = "\n\n--\nSerwis DAR-GAZ\nSamsonowicza 18K\nOstrowiec Sw.\ntel. 412474575";

    public function getMessageFormattedAttribute(): string
    {
        return trim(explode('--', $this->message)[0]);
    }

    public function getSmsAmountAttribute(): int
    {
        return ceil(strlen($this->message) / self::SMS_LENGTH);
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('Y-m-d H:i');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function zlecenie()
    {
        return $this->hasOne('App\Models\Zlecenie\Zlecenie', 'id_zlecenia', 'zlecenie_id');
    }
}
