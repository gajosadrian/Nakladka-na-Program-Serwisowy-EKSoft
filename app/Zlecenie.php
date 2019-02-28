<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Zlecenie extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Zlecenia';
    protected $primaryKey = 'id_zlecenia';
    public $timestamps = false;

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
                'nazwa' => 'Telefon',
                'icon' => 'fa fa-info-circle',
            ],
            3 => (object) [
                'nazwa' => 'WWW',
                'icon' => 'fa fa-info-circle',
            ],
            4 => (object) [
                'nazwa' => 'Osobiście',
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

    public function getIsZakonczoneAttribute(): bool
    {
        return in_array($this->status_id, Zlecenie_Status::$ZAKONCZONE_IDS) or $this->archiwalny or $this->anulowany;
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

    public function setOpisAttribute(string $value): void
    {
        $this->attributes['OpisZlec'] = $value;
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

    public function getCzasTrwaniaAttribute(): int
    {
        $data = Carbon::now();
        if ($this->is_zakonczone) $data = $this->data_zakonczenia;
        return $this->data_przyjecia->startOfDay()->diffInDays($data->startOfDay());
    }

    public function getCzasTrwaniaFormattedAttribute(): string
    {
        $str = $this->czas_trwania;
        $str .= ($this->czas_trwania == 1) ? ' dzień' : ' dni';
        return $str;
    }

    public function getErrorsAttribute(): array
    {
        $array = [];

        if ($this->dni_od_zakonczenia > 2 and in_array($this->status_id, [Zlecenie_Status::UMOWIONO_ID, Zlecenie_Status::GOTOWE_DO_WYJAZDU_ID, Zlecenie_Status::NA_WARSZTACIE_ID, Zlecenie_Status::NIE_ODBIERA_TEL_ID, Zlecenie_Status::PONOWNA_WIZYTA_ID]))
            $array[] = 'Zlecenie niezamknięte';

        return $array;
    }

    /**
    * Scopes
    *
    */

    public function scopeNiezakonczone($query)
    {
        foreach (Zlecenie_Status::$ZAKONCZONE_IDS as $status_id) {
            $query->where('id_status', '!=', $status_id);
        }
        return $query->where('Archiwalny', false)->where('Anulowany', null);
    }

    /**
    * Relations
    *
    */

    public function klient()
    {
        return $this->hasOne('App\Models\Subiekt\Subiekt_Kontrahent', 'kh_Id', 'id_firmy');
    }

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

    public function kosztorys_pozycje()
    {
        return $this->hasMany('App\Zlecenie_Kosztorys_Pozycja', 'id_zs', 'id_zlecenia');
    }

    /**
    * Scopes
    *
    */

    public function scopeTechnik($query, $technik_id)
    {
        return $query->where('id_o_technika', $technik_id);
    }

    /**
    * Methods
    *
    */

    public function getNiezakonczone(array $data = [])
    {
        $data = (object) $data;
        $query = $this->with('klient', 'status', 'terminarz', 'urzadzenie', 'kosztorys_pozycje')->niezakonczone()->oldest('DataKoniec');
        if (@$data->technik_id) {
            $query->technik($data->technik_id);
        }
        return $query->get()->sortByDesc('dni_od_zakonczenia');
    }
}
