<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Zlecenie extends Model
{
    protected $table = 'ser_Zlecenia';
    protected $primaryKey = 'id_zlecenia';

    /**
     * Attributes
     *
     */

    public function getIdAttribute($value): string
    {
        return $this->id_zlecenia;
    }

    public function getNrAttribute($value): string
    {
        return $this->NrZlecenia;
    }

    public function getStatusIdAttribute($value)
    {
        return $this->id_status ?? false;
    }

    public function getArchiwalnyAttribute($value): bool
    {
        return $this->attributes['Archiwalny'];
    }

    public function getAnulowanyAttribute($value): bool
    {
        return $this->attributes['Anulowany'] ?? false;
    }

    public function getDataZakonczeniaAttribute($value)
    {
        return $this->DataKoniec ? Carbon::parse($this->DataKoniec)->toDateString() : false;
    }

    public function getDataPrzyjeciaAttribute($value)
    {
        return $this->attributes['DataPrzyjecia'] ? Carbon::parse($this->attributes['DataPrzyjecia'])->toDateString() : false;
    }

    public function getOpisAttribute()
    {
        return $this->OpisZlec;
    }

    public function getOpisBrAttribute($value='')
    {
        return nl2br($this->opis);
    }

    public function getDniOdZakonczeniaAttribute($value): int
    {
        return Carbon::parse($this->DataKoniec)->diffInDays(Carbon::now()->endOfDay(), false);
    }

    public function getDniOdPrzyjeciaAttribute($value): int
    {
        return Carbon::parse($this->DataPrzyjecia)->diffInDays(Carbon::now()->endOfDay(), false);
    }

    /**
     * Relations
     *
     */

    public function status()
    {
        return $this->hasOne('App\Zlecenie_Status', 'id_stat', 'id_status')->withDefault([
            'id_stat' => false,
            'status' => 'Brak statusu',
        ]);
    }

    /**
     * Methods
     *
     */

    public function getNiezakonczone()
    {
        foreach (Zlecenie_Status::$id_zakonczonych as $status_id) {
            $this->where('status_id', '!=', $status_id);
        }
        return $this->where('Archiwalny', false)->where('Anulowany', null)->orderBy('DataKoniec')->get()->sortByDesc('dni_od_zakonczenia');
    }
}
