<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;
use App\Models\Zlecenie\Terminarz;

class Technik extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Technicy';
    protected $primaryKey = 'id_technika';
    protected $visible = ['id', 'imie', 'nazwisko'];
    protected $appends = ['id', 'imie', 'nazwisko'];
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

    public function getAkronimAttribute(): string
    {
        $words = explode(' ', $this->nazwa);
        $acronym = '';
        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        return $acronym;
    }

    /**
     * Methods
     *
     */
    public function getArray(): array
    {
        return [
            'id' => $this->id,
            'nazwa' => $this->nazwa,
        ];
    }

    public static function getLast()
    {
        $technik_ids = Terminarz::with('technik')->orderByDesc('STARTDATE')->limit(100)->get()->pluck('technik.id')->unique()->values();
        return self::whereIn('id_technika', $technik_ids)->get()->sortBy('imie')->sortBy('nazwisko'); // nazwa
    }

    /**
     * Relations
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'id_technika', 'technik_id');
    }
}
