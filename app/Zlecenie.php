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

    public function getZrodloAttribute(): object
    {
        $array = [
            0 => (object) [
                'nazwa' => 'zz',
                'icon' => 'fa fa-info-circle',
            ],
            3 => (object) [
                'nazwa' => 'xx',
                'icon' => 'fa fa-info-circle',
            ],
            4 => (object) [
                'nazwa' => 'yy',
                'icon' => 'fa fa-info-circle',
            ],
            '_default' => (object) [
                'nazwa' => '-',
                'icon' => '',
            ],
        ];
        return $array[$this->attributes['Zamowienie_obce']] ?? $array['_default'];
    }

    public function getZnacznikAttribute(): object
    {
        $array = [
            'A' => (object) [
                'nazwa' => 'Gwarancja',
                'icon' => 'fa fa-shield-alt',
                'color' => false,
            ],
            'B' => (object) [
                'nazwa' => 'Odpłatne',
                'icon' => 'fa fa-dollar-sign',
                'color' => false,
            ],
            'H' => (object) [
                'nazwa' => 'Ubezpieczenie',
                'icon' => 'fa fa-hands-helping',
                'color' => false,
            ],
            '_default' => (object) [
                'nazwa' => 'Inne',
                'icon' => 'far fa-bookmark',
                'color' => false,
            ],
        ];
        return $array[$this->attributes['Z']] ?? $array['_default'];
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
        return $this->terminarz->is_data_zakonczenia ? $this->terminarz->data_zakonczenia : Carbon::parse($this->attributes['DataKoniec']);
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

    public function getErrorsAttribute(): array
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
        $dni_od_zakonczenia = 5;

        $query = $this->with('status', 'terminarz', 'urzadzenie');
        $query->where(function($q) {
            foreach (Zlecenie_Status::$ZAKONCZONE_IDS as $status_id) {
                $q->where('id_status', '!=', $status_id);
            }
            $q->where('Archiwalny', false)->where('Anulowany', null);
        });
        $collection = $query->oldest('DataKoniec')->get();
        return $collection->sortByDesc('dni_od_zakonczenia');
    }
}
