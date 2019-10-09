<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class Subiekt_Towar extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tw__Towar';
    protected $primaryKey = 'tw_Id';
    protected $with = ['zdjecia'];

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

    public function getRodzajAttribute(): int
    {
        return $this->attributes['tw_Rodzaj'] ?? false;
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

    /**
     * Relations
     *
     */

    public function zdjecia()
    {
        return $this->hasMany('App\Models\Subiekt\TowarZdjecie', 'zd_IdTowar', 'tw_Id')->orderByDesc('zd_Glowne');
    }
}
