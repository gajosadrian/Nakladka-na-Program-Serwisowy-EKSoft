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

    public function getNazwaAttribute(): string
    {
        return $this->attributes['tw_Nazwa'];
    }

    public function getOpisAttribute(): string
    {
        return $this->attributes['tw_Opis'];
    }

    public function getSymbolAttribute(): string
    {
        return $this->attributes['tw_Symbol'];
    }

    public function getSymbolDostawcyAttribute(): string
    {
        return $this->attributes['tw_DostSymbol'];
    }
}
