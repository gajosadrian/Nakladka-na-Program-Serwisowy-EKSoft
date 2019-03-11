<?php

namespace App\Models\Rozliczenie;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rozliczenie extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rozliczenia';
    protected $dates = ['closed_at'];

    /**
     * Attributes
     *
     */

    public function getNrAttribute(): string
    {
        return $this->data->format('Y-m');
    }

    public function getOkresAttribute(): string
    {
        return $this->nr;
    }

    public function getDataAttribute(): Carbon
    {
        return Carbon::create($this->rok, $this->miesiac)->endOfMonth()->endOfDay();
    }

    public function getRozliczylAttribute(): string
    {
        return $this->_pracownik->nazwa;
    }

    /**
     * Methods
     *
     */

    public static function getLast()
    {
        return self::latest()->first();
    }

    /**
     * Relations
     *
     */

    public function zlecenia()
    {
        return $this->hasMany('App\Models\Rozliczenie\RozliczoneZlecenie');
    }

    public function _pracownik()
    {
        return $this->hasOne('App\Models\SMS\Pracownik', 'LOGIN', 'pracownik');
    }
}
