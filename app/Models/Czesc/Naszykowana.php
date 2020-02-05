<?php

namespace App\Models\Czesc;

// use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Database\Eloquent\Model;

class Naszykowana extends Model
{
    protected $connection = 'mysql';
    protected $table = 'naszykowane_czesci';
    protected $guarded = [];

    /**
     * Attributes
     *
     */

    public function getIloscZwroconeAttribute(): float
    {
        return $this->ilosc - $this->ilosc_zamontowane - $this->ilosc_do_zwrotu;
    }
}
