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
        return $this->attributes['id_zlecenia'];
    }

    public function getNrAttribute($value): string
    {
        return $this->attributes['NrZlecenia'];
    }

    public function getStatusIdAttribute($value)
    {
        return $this->attributes['id_status'] ?? false;
    }

    public function getArchiwalnyAttribute($value): bool
    {
        return $this->attributes['Archiwalny'] ?? false;
    }

    public function getAnulowanyAttribute($value): bool
    {
        return $this->attributes['Anulowany'] ?? false;
    }

    public function getIsDataPrzyjeciaAttribute($value): bool
    {
        return $this->attributes['DataPrzyjecia'] ? true : false;
    }

    public function getDataPrzyjeciaAttribute($value): Carbon
    {
        return Carbon::parse($this->attributes['DataPrzyjecia']);
    }

    public function getDataPrzyjeciaFormattedAttribute($value): String
    {
        return $this->is_data_przyjecia ? $this->data_przyjecia->format('Y-m-d H:i') : 'Brak daty przyjęcia';
    }

    public function getIsDataZakonczeniaAttribute($value): bool
    {
        return $this->terminarz->is_data_zakonczenia;
    }

    public function getDataZakonczeniaAttribute($value): Carbon
    {
        return $this->terminarz->data_zakonczenia;
    }

    public function getDataZakonczeniaFormattedAttribute($value): String
    {
        return $this->terminarz->data_zakonczenia_formatted;
    }

    public function getGodzinaZakonczeniaAttribute($value): String
    {
        return $this->terminarz->godzina_zakonczenia;
    }

    public function getOpisAttribute($value): string
    {
        return $this->attributes['OpisZlec'];
    }

    public function getOpisBrAttribute($value): string
    {
        return nl2br($this->opis);
    }

    public function getDniOdZakonczeniaAttribute($value): int
    {
        return $this->data_zakonczenia->diffInDays(Carbon::now()->endOfDay(), false);
    }

    public function getDniOdPrzyjeciaAttribute($value): int
    {
        return $this->data_przyjecia->diffInDays(Carbon::now()->endOfDay(), false);
    }

    public function getErrorAttribute($value)
    {
        // return Carbon::parse(null);
    }

    /**
    * Relations
    *
    */

    public function status()
    {
        return $this->hasOne('App\Zlecenie_Status', 'id_stat', 'id_status')->withDefault([
            'status' => 'Brak statusu',
        ]);
    }

    public function terminarz()
    {
        return $this->hasOne('App\Zlecenie_Terminarz', 'ID_ZLECENIA', 'id_zlecenia')->withDefault([
            'STARTDATE' => false,
            'ENDDATE' => false,
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
