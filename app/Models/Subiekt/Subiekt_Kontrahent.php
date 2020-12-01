<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class Subiekt_Kontrahent extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'kh__Kontrahent';
    protected $primaryKey = 'kh_Id';
    protected $with = ['ewidencja'];

    /**
    * Attributes
    *
    */

    public function getIdAttribute(): string
    {
        return $this->attributes['kh_Id'];
    }

    public function getSymbolAttribute(): string
    {
        return $this->attributes['kh_Symbol'];
    }

    public function getDaneAttribute(): object
    {
        return $this->ewidencja;
    }

    public function getNazwaAttribute(): string
    {
        return $this->dane->pelna_nazwa;
    }

    public function getTelefonAttribute(): string
    {
        return $this->dane->telefon;
    }

    public function getKomorkowyAttribute(): ?string
    {
        foreach ($this->dane->telefony as $telefon) {
            if ($telefon->is_komorkowy) {
                return $telefon->nr ?: null;
            }
        }
        return null;
    }

    public function getKomorkowyNumericAttribute(): ?string
    {
        foreach ($this->dane->telefony as $telefon) {
            if ($telefon->is_komorkowy) {
                return $telefon->phone_nr ?: null;
            }
        }
        return null;
    }

    public function getAdresRawAttribute(): string
    {
        return $this->dane->adres;
    }

    public function getAdresAttribute(): string
    {
        $adres = $this->adres_raw;
        if (str_contains($adres, ',')) {
            $adres = explode(',', $adres)[1];
        }
        return $adres;
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

    public function getUlicaAttribute(): string
    {
        return $this->dane->ulica;
    }

    public function getMiastoRawAttribute(): string
    {
        $miejscowosc = $this->dane->miejscowosc;
        if (str_contains($miejscowosc, 'Ostrowiec Św')) {
            $miejscowosc = 'Ostrowiec Świętokrzyski';
        }
        return $miejscowosc;
    }

    public function getMiastoAttribute(): string
    {
        $adres = $this->adres_raw;
        if (str_contains($adres, ',')) {
            $miasto = explode(',', $adres)[0];
        } else {
            $miasto = $this->miasto_raw;
        }
        return $miasto;
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
        return $this->dane->telefony_array;
    }

    public function getTelefonyFormattedAttribute(): string
    {
        return implode(', ', $this->telefony_array);
    }

    /**
     * Relations
     *
     */

    public function ewidencja()
    {
        return $this->hasOne('App\Models\Subiekt\KontrahentEwidencja', 'adr_IdObiektu', 'kh_Id')->where('adr_TypAdresu', 1);
    }
}
