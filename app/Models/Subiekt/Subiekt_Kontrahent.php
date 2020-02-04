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

    public function getAdres2Attribute(): string
    {
        $adres = $this->adres;
        $adres_arr = explode('/', $adres);
        if (count($adres_arr) > 1) {
            array_pop($adres_arr);
        }
        return implode('/', $adres_arr);
    }

    public function getMiastoAttribute(): string
    {
        $miejscowosc = $this->dane->miejscowosc;
        if (str_contains($miejscowosc, 'Ostrowiec Św')) {
            $miejscowosc = 'Ostrowiec Świętokrzyski';
        }
        return $miejscowosc;
    }

    public function getKodPocztowyAttribute(): string
    {
        return $this->dane->kod_pocztowy;
    }

    public function getTelefonyAttribute(): object
    {
        return $this->dane->telefony;
    }

    public function getTelefonyArrayAttribute(): array
    {
        return $this->telefony->pluck('tel_Numer')->toArray();
    }

    public function getTelefonyFormattedAttribute(): string
    {
        return implode(', ', $this->telefony_array);
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
