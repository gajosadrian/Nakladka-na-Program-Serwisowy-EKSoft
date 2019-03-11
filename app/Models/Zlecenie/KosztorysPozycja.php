<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class KosztorysPozycja extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_ZlecKosztPoz';
    protected $with = ['towar'];

    /**
    * Attributes
    *
    */

    public function getNazwaAttribute(): string
    {
        return $this->towar->nazwa;
    }

    public function getSymbolAttribute(): string
    {
        return $this->towar->symbol;
    }

	public function getOpisAttribute(): string
    {
        return $this->attributes['opis_dodatkowy'] ?? false;
    }

    public function getCenaAttribute(): float
    {
        return round($this->attributes['cena'], 4);
    }

    public function getCenaFormattedAttribute(): string
    {
        return number_format($this->cena, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getIloscAttribute(): float
    {
        return round($this->attributes['ilosc'], 2);
    }

    public function getWartoscAttribute(): float
    {
        return round($this->cena * $this->ilosc, 4);
    }

    public function getWartoscFormattedAttribute(): string
    {
        return number_format($this->wartosc, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getWartoscBruttoAttribute(): float
    {
        return round($this->wartosc * ($this->vat + 1), 4);
    }

    public function getWartoscBruttoFormattedAttribute(): string
    {
        return number_format($this->wartosc_brutto, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getIsCzescAttribute(): bool
    {
        return is_numeric($this->symbol) or $this->opis;
    }

    /**
    * Relations
    *
    */

    public function towar()
    {
        return $this->hasOne('App\Models\Subiekt\Subiekt_Towar', 'tw_Id', 'id_o_tw')->withDefault([
            'tw_Nazwa' => '-',
            'tw_Opis' => '',
            'tw_Symbol' => '-',
            'tw_DostSymbol' => '-',
        ]);
    }
}
