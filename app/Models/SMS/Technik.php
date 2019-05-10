<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;
use App\Models\Zlecenie\Terminarz;

class Technik extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Technicy';
    protected $primaryKey = 'id_technika';
    public $timestamps = false;

    /**
    * Attributes
    *
    */

    public function getIdAttribute(): int
    {
        return $this->attributes['id_technika'];
    }

    public function getNazwaAttribute(): string
    {
        return $this->imie . ' ' . $this->nazwisko;
    }

    public function getNazwaReversedAttribute(): string
    {
        return $this->nazwisko . ' ' . $this->imie;
    }

    public function getImieAttribute(): string
    {
        return $this->attributes['Imie'];
    }

    public function getNazwiskoAttribute(): string
    {
        return str_replace('.', '', $this->attributes['Nazwisko']);
    }

    /**
     * Methods
     *
     */

    public static function getLast()
    {
        $technik_ids = Terminarz::with('technik')->orderByDesc('STARTDATE')->limit(100)->get()->pluck('technik.id')->unique()->values();
        return self::whereIn('id_technika', $technik_ids)->get()->sortBy('imie')->sortBy('nazwisko'); // nazwa
    }
}
