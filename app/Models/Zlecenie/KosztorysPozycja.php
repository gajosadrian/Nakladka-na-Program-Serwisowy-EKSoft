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

    public function getTowarIdAttribute(): int
    {
        return $this->attributes['id_o_tw'];
    }

    public function getZlecenieIdAttribute(): int
    {
        return $this->attributes['id_zs'];
    }

    public function getNazwaAttribute(): string
    {
        return $this->towar->nazwa;
    }

    public function getSymbolAttribute(): string
    {
        return $this->towar->symbol;
    }

    public function getSymbolDostawcyAttribute(): string
    {
        $str = $this->zamiennik ?: $this->towar->symbol_dostawcy;
        if ($this->zamiennik) {
            $str = '*' . $str;
        }
        return $str;
    }

    public function getSymbolDostawcy2Attribute(): string
    {
        return $this->towar->symbol_dostawcy2;
    }

	public function getOpisAttribute(): string
    {
        return $this->attributes['opis_dodatkowy'] ?? false;
    }

	public function getOpisFixedAttribute(): string
    {
        $opis = $this->opis;
        if ($opis == '') return false;
        $opis = preg_replace("/\[[^)]+\]/", '', $opis);
        return $opis;
    }

    public function getCenaAttribute(): float
    {
        return round($this->attributes['cena'], 4);
    }

    public function getCenaFormattedAttribute(): string
    {
        return number_format($this->cena, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getCenaBruttoAttribute(): float
    {
        return round($this->cena * ($this->vat + 1), 4);
    }

	public function getCenaBruttoFormattedAttribute(): string
    {
        return number_format($this->cena_brutto, 2, '.', ' ') . ' zł'; // &nbsp;
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

    public function getPolkaAttribute(): string
    {
        return $this->towar->polka;
    }

    public function getZamiennikAttribute(): string
    {
        preg_match('#\[(.*?)\]#', $this->opis, $match);
        if (@isset( $match[1] )) {
            return $match[1];
        }
        return false;
    }

    /**
    * Relations
    *
    */

    public function zlecenie()
    {
        return $this->belongsTo('App\Models\Zlecenie\Zlecenie', 'id_zs', 'id_zlecenia');
    }

    public function towar()
    {
        return $this->hasOne('App\Models\Subiekt\Subiekt_Towar', 'tw_Id', 'id_o_tw')->withDefault([
            'tw_Nazwa' => '-',
            'tw_Opis' => '',
            'tw_Symbol' => '-',
            'tw_DostSymbol' => '',
            'tw_PKWiU' => '',
        ]);
    }
}
