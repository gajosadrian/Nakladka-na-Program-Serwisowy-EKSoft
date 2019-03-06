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

    public function getNrAttribute()
    {
        return Carbon::create($this->rok, $this->miesiac)->format('Y-m');
    }

    /**
     * Methods
     *
     */

    public static function getLast()
    {
        return self::latest()->first();
    }
}
