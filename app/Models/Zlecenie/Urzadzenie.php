<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class Urzadzenie extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'Maszyny';
    protected $primaryKey = 'ID_MASZYNY';
    public $timestamps = false;

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
        return $this->attributes['KATEGORIA'] ?? false;
    }

    public function getNazwaAttribute(): string
    {
        return $this->attributes['NAZWA_MASZ'] ?? false;
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

    /**
     * Relations
     *
     */

    public function zlecenie()
    {
        return $this->belongsTo(Zlecenie::class, 'ID_MASZYNY', 'id_maszyny')->latest('ID_MASZYNY');
    }

    // ERROR: MySQL <-> MsSQL
    // public function zdjecia()
    // {
    //     return $this->hasManyThrough(
    //         Zdjecie::class,
    //         Zlecenie::class,
    //         'id_maszyny',
    //         'urzadzenie_id',
    //         'ID_MASZYNY',
    //         'id_maszyny'
    //     );
    // }
}
