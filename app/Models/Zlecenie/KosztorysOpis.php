<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class KosztorysOpis extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_ZlecKosztOpis';

    /**
    * Attributes
    *
    */

    public function getOpisAttribute(): string
    {
        return $this->attributes['opis'] ?? false;
    }
}
