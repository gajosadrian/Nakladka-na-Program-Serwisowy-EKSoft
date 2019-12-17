<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mysql';
    protected $table = 'zlecenie_logs';
    protected $casts = [
        'zlecenie_data' => 'date',
    ];

    public const TYPE_OPIS = 1;
    public const TYPE_STATUS = 2;

    /**
    * Attributes
    *
    */
    public function getIsTerminowoAttribute(): bool
    {
        return $this->terminowo;
    }

    public function getIsOpisAttribute(): bool
    {
        return $this->type == self::TYPE_OPIS;
    }

    public function getIsStatusAttribute(): bool
    {
        return $this->type == self::TYPE_STATUS;
    }

    /**
    * Relations
    *
    */
    public function zlecenie()
    {
        return $this->belongsTo(Zlecenie::class, 'zlecenie_id', 'id_zlecenia');
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'id_stat', 'status_id');
    }
}
