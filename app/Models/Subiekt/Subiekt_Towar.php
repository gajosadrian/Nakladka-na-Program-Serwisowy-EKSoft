<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class Subiekt_Towar extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tw__Towar';
    protected $primaryKey = 'tw_Id';

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

    public function getSymbolDostawcy2Attribute(): string
    {
        return $this->attributes['tw_Pole3'] ?? false;
    }

    public function getPolkaAttribute(): string
    {
        return $this->attributes['tw_PKWiU'] ?? false;
    }

    public function getRodzajAttribute(): int
    {
        return $this->attributes['tw_Rodzaj'] ?? false;
    }
}
