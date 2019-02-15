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

    public function getIdAttribute(): string
    {
        return $this->attributes['id_zlecenia'];
    }

    public function getKlientIdAttribute(): int
    {
        return $this->attributes['id_firmy'];
    }

    public function getUrzadzenieIdAttribute(): int
    {
        return $this->attributes['id_maszyny'] ?? false;
    }

    public function getNrAttribute(): string
    {
        return $this->attributes['NrZlecenia'];
    }

    public function getNrObcyAttribute(): string
    {
        return $this->attributes['NrObcy'] ?? false;
    }

    public function getStatusIdAttribute(): int
    {
        return $this->attributes['id_status'] ?? false;
    }

    public function getArchiwalnyAttribute(): bool
    {
        return $this->attributes['Archiwalny'] ?? false;
    }

    public function getAnulowanyAttribute(): bool
    {
        return $this->attributes['Anulowany'] ?? false;
    }

    public function getIsDataPrzyjeciaAttribute(): bool
    {
        return $this->attributes['DataPrzyjecia'] ? true : false;
    }

    public function getDataPrzyjeciaAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['DataPrzyjecia']);
    }

    public function getDataPrzyjeciaFormattedAttribute(): String
    {
        return $this->is_data_przyjecia ? $this->data_przyjecia->format('Y-m-d H:i') : 'Brak daty przyjęcia';
    }

    public function getIsDataZakonczeniaAttribute(): bool
    {
        return $this->terminarz->is_data_zakonczenia;
    }

    public function getDataZakonczeniaAttribute(): Carbon
    {
        return $this->terminarz->data_zakonczenia;
    }

    public function getDataZakonczeniaFormattedAttribute(): String
    {
        return $this->terminarz->data_zakonczenia_formatted;
    }

    public function getGodzinaZakonczeniaAttribute(): String
    {
        return $this->terminarz->godzina_zakonczenia;
    }

    public function getOpisAttribute(): string
    {
        return $this->attributes['OpisZlec'];
    }

    public function getOpisBrAttribute(): string
    {
        return nl2br($this->opis);
    }

    public function getDniOdZakonczeniaAttribute(): int
    {
        return $this->data_zakonczenia->diffInDays(Carbon::now()->endOfDay(), false);
    }

    public function getDniOdPrzyjeciaAttribute(): int
    {
        return $this->data_przyjecia->diffInDays(Carbon::now()->endOfDay(), false);
    }

    public function getErrorAttribute()
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

    public function urzadzenie()
    {
        return $this->hasOne('App\Zlecenie_Urzadzenie', 'ID_MASZYNY', 'id_maszyny')->withDefault([
            'NAZWA_MASZ' => 'Brak urządzenia',
            'KATEGORIA' => false,
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
