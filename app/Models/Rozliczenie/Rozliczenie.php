<?php

namespace App\Models\Rozliczenie;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rozliczenie extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rozliczenia';
    protected $dates = ['closed_at'];
    protected $casts = [
        'robocizny' => 'array',
        'dojazdy' => 'array',
    ];

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

    public function getMonthAttribute(): int
    {
        return $this->data->format('n');
    }

    public function getDataAttribute(): Carbon
    {
        return Carbon::create($this->rok, $this->miesiac, 1)->endOfMonth()->endOfDay();
    }

    public function getRozliczylAttribute(): string
    {
        return $this->_pracownik->nazwa;
    }

    public function getSumaRobociznAttribute(): float
    {
        return array_sum($this->robocizny);
    }

    public function getSumaRobociznFormattedAttribute(): string
    {
        return number_format($this->suma_robocizn, 0, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getSumaDojazdowAttribute(): float
    {
        return array_sum($this->dojazdy);
    }

    public function getSumaDojazdowFormattedAttribute(): string
    {
        return number_format($this->suma_dojazdow, 0, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getZleceniodawcyAttribute(): object
    {
        return $this->rozliczone_zlecenia->unique('zleceniodawca')->pluck('zleceniodawca')->sort()->values();
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

    public function rozliczone_zlecenia()
    {
        return $this->hasMany('App\Models\Rozliczenie\RozliczoneZlecenie');
    }

    public function _pracownik()
    {
        return $this->hasOne('App\Models\SMS\Pracownik', 'LOGIN', 'pracownik');
    }
}
