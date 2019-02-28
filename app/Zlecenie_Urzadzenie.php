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

    public function getModelAttribute(): string
    {
        $typ = $this->attributes['TYP_MASZ'] ?? false;
        if (substr($typ, 0, 1) == '!') {
            return false;
        }

        return $typ;
    }

    public function getKodWyrobuAttribute(): string
    {
        return $this->attributes['asset'] ?? false;
    }

    public function getNrSeryjnyAttribute(): string
    {
        $nr_seryjny = $this->attributes['SERIAL_NO'] ?? false;
        if (substr($nr_seryjny, 0, 3) == 'FK0') {
            return false;
        }

        return $nr_seryjny;
    }
}
