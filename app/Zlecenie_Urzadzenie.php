<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zlecenie_Urzadzenie extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'Maszyny';
    protected $primaryKey = 'ID_MASZYNY';

    /**
    * Attributes
    *
    */

    public function getIdAttribute(): int
    {
        return $this->attributes['ID_MASZYNY'];
    }

    public function getProducentAttribute(): string
    {
        return $this->attributes['KATEGORIA'];
    }

    public function getNazwaAttribute(): string
    {
        return $this->attributes['NAZWA_MASZ'];
    }
}
