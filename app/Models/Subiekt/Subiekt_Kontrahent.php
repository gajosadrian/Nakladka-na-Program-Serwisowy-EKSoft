<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class Subiekt_Kontrahent extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'kh__Kontrahent';
    protected $primaryKey = 'kh_Id';
    protected $with = ['ewidencje'];

    /**
    * Attributes
    *
    */

    public function getSymbolAttribute(): string
    {
        return $this->attributes['kh_Symbol'];
    }

    public function getDaneAttribute(): object
    {
        return $this->ewidencje->where('typ_adresu', 1)->first();
    }

    public function getNazwaAttribute(): string
    {
        return $this->dane->pelna_nazwa;
    }

    public function getTelefonAttribute(): string
    {
        return $this->dane->telefon;
    }

    public function getAdresAttribute(): string
    {
        return $this->dane->adres;
    }

    public function getMiastoAttribute(): string
    {
        return $this->dane->miejscowosc;
    }

    public function getKodPocztowyAttribute(): string
    {
        return $this->dane->kod_pocztowy;
    }

    /**
     * Relations
     *
     */

    public function ewidencje()
    {
        return $this->hasMany('App\Models\Subiekt\KontrahentEwidencja', 'adr_IdObiektu', 'kh_Id');
    }
}
