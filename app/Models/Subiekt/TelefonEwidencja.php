<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class TelefonEwidencja extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tel__Ewid';
    protected $primaryKey = 'tel_Id';
    protected $cast = [
        'tel_Podstawowy' => 'boolean',
    ];

    /**
    * Attributes
    *
    */

    public function getNrAttribute(): string
    {
        return $this->attributes['tel_Numer'];
    }

    public function getIsPodstawowyAttribute(): bool
    {
        return $this->attributes['tel_Podstawowy'];
    }
}
