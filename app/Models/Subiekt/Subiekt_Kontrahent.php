<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class Subiekt_Kontrahent extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'kh__Kontrahent';
    protected $primaryKey = 'kh_Id';

    /**
    * Attributes
    *
    */

    public function getImieAttribute(): string
    {
        return $this->attributes['kh_Imie'];
    }

    public function getNazwiskoAttribute(): string
    {
        return $this->attributes['kh_Nazwisko'];
    }

    public function getSymbolAttribute(): string
    {
        return $this->attributes['kh_Symbol'];
    }

    public function getNazwaAttribute(): string
    {
        return $this->nazwisko . ' ' . $this->imie; // &nbsp;
    }
}
