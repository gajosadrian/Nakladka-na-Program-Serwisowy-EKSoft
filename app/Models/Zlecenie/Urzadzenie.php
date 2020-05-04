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
        $producent = $this->attributes['KATEGORIA'] ?? false;
        if (substr($producent, 0, 1) == '!') {
            return false;
        }
        return $producent;
    }

    public function setProducentAttribute(string $val): void
    {
        $this->attributes['KATEGORIA'] = $val;
    }

    public function getNazwaAttribute(): string
    {
        return $this->attributes['NAZWA_MASZ'] ?? false;
    }

    public function setNazwaAttribute(string $val): void
    {
        $this->attributes['NAZWA_MASZ'] = $val;
    }

    public function getModelRawAttribute(): ?string
    {
        return $this->attributes['TYP_MASZ'] ?? null;
    }

    public function getModelAttribute(): string
    {
        $model = $this->model_raw ?? false;
        if (substr($model, 0, 1) == '!') {
            return false;
        }
        return $model;
    }

    public function setModelAttribute(string $val): void
    {
        $this->attributes['TYP_MASZ'] = $val;
    }

    public function getKodWyrobuAttribute(): string
    {
        return $this->attributes['asset'] ?? false;
    }

    public function setKodWyrobuAttribute(?string $val): void
    {
        $this->attributes['asset'] = $val;
    }

    public function getNrSeryjnyRawAttribute(): ?string
    {
        return $this->attributes['SERIAL_NO'] ?? null;
    }

    public function getNrSeryjnyAttribute(): string
    {
        $nr_seryjny = $this->nr_seryjny_raw ?? false;
        if (substr($nr_seryjny, 0, 3) == 'FK0') {
            return false;
        }
        return $nr_seryjny;
    }

    public function setNrSeryjnyAttribute(string $val): void
    {
        $this->attributes['SERIAL_NO'] = $val;
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
