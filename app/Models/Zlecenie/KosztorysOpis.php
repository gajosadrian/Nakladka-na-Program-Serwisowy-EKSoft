<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class KosztorysOpis extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_ZlecKosztOpis';
    protected $guarded = [];
    public $timestamps = false;

    /**
    * Attributes
    *
    */

    public function getOpisAttribute(): string
    {
        return $this->attributes['opis'] ?? false;
    }

    public function setOpisAttribute(?string $opis): void
    {
        $this->attributes['opis'] = $opis ?: null;
    }
}
