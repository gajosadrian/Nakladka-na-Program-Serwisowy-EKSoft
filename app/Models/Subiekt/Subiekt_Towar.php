<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class Subiekt_Towar extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tw__Towar';
    protected $primaryKey = 'tw_Id';
    protected $with = ['zdjecia'];

    public $timestamps = false;

    /**
    * Attributes
    *
    */

    public function getIdAttribute(): int
    {
        return $this->attributes['tw_Id'];
    }

    public function getNazwaAttribute(): string
    {
        return $this->attributes['tw_Nazwa'];
    }

    public function getOpisAttribute(): string
    {
        return $this->attributes['tw_Opis'] ?? false;
    }

    public function getSymbolAttribute(): string
    {
        return $this->attributes['tw_Symbol'];
    }

    public function getSymbolDostawcyAttribute(): string
    {
        return $this->attributes['tw_DostSymbol'] ?? false;
    }

    public function getSymbolDostawcyMinAttribute()
    {
        return str_replace(['-', '.'], '', $this->symbol_dostawcy);
    }

    public function getSymbolDostawcy2Attribute(): string
    {
        return $this->attributes['tw_Pole3'] ?? false;
    }

    public function getSymbolDostawcy2MinAttribute()
    {
        return str_replace(['-', '.'], '', $this->symbol_dostawcy2);
    }

    public function getPolkaAttribute(): string
    {
        return $this->attributes['tw_PKWiU'] ?? false;
    }

    public function setPolkaAttribute(string $value): void
    {
        $this->attributes['tw_PKWiU'] = $value;
    }

    public function getRodzajAttribute(): int
    {
        return $this->attributes['tw_Rodzaj'] ?? false;
    }

    public function getJednostkaAttribute(): string
    {
        return $this->attributes['tw_JednMiary'];
    }

    public function getIsZdjecieAttribute(): bool
    {
        return count($this->zdjecia) > 0;
    }

    public function getZdjecieBinaryAttribute()
    {
        return $this->zdjecia[0]->zdjecie_binary;
    }

    public function getZdjecieUrlAttribute(): string
    {
        return route('zdjecie_towaru', ['id' => $this->id]);
    }

    public function getIsTowarAttribute(): bool
    {
        return $this->rodzaj == 1;
    }

    public function getIsUslugaAttribute(): bool
    {
        return $this->rodzaj == 2;
    }

    /**
     * Relations
     *
     */

    public function stan()
    {
        return $this->hasOne(TowarStan::class, 'st_TowId', 'tw_Id')->where('st_MagId', 1);
    }

    public function vat()
    {
        return $this->hasOne(TowarVat::class, 'vat_Id', 'tw_IdVatSp');
    }

    public function cena()
    {
        return $this->hasOne(TowarCena::class, 'tc_IdTowar', 'tw_Id');
    }

    public function zdjecia()
    {
        return $this->hasMany('App\Models\Subiekt\TowarZdjecie', 'zd_IdTowar', 'tw_Id')->orderByDesc('zd_Glowne');
    }

    public function inwentaryzacja_stany()
    {
        return $this->hasMany('App\Models\Inwentaryzacja\Stan', 'towar_id', 'tw_Id');
    }
}
