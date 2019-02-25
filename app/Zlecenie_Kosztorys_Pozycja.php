<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zlecenie_Kosztorys_Pozycja extends Model
{
    protected $table = 'ser_ZlecKosztPoz';
    protected $with = ['towar'];

    /**
    * Attributes
    *
    */

	public function getOpisAttribute(): string
    {
        return $this->attributes['opis_dodatkowy'] ?? false;
    }

    public function getCenaAttribute(): float
    {
        return round($this->attributes['cena'], 2);
    }

    public function getIloscAttribute(): float
    {
        return round($this->attributes['ilosc'], 2);
    }

    public function getWartoscAttribute(): float
    {
        return round($this->cena * $this->ilosc, 2);
    }

    public function getWartoscBruttoAttribute(): float
    {
        return round($this->wartosc * ($this->vat + 1), 2);
    }

    /**
    * Relations
    *
    */

    public function towar()
    {
        return $this->hasOne('App\Models\Subiekt\Subiekt_Towar', 'tw_Id', 'id_o_tw');
    }
}
