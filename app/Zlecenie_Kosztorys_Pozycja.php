<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zlecenie_Kosztorys_Pozycja extends Model
{
    protected $table = 'ser_ZlecKosztPoz';

    /**
    * Attributes
    *
    */

	public function getOpisAttribute(): string
    {
        return $this->attributes['opis_dodatkowy'] ?? false;
    }
}
