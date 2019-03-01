<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Zlecenie_Terminarz extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Terminarz';
    protected $primaryKey = 'ID_TERMINU';

    /**
     * Attributes
     *
     */

    public function getIsDataRozpoczeciaAttribute($value): bool
    {
        return $this->STARTDATE ? true : false;
    }

    public function getDataRozpoczeciaAttribute($value): Carbon
    {
        return Carbon::parse($this->STARTDATE);
    }

    public function getDataRozpoczeciaFormattedAttribute($value): string
    {
        return $this->is_data_rozpoczecia ? $this->data_rozpoczecia->format('Y-m-d H:i') : 'Brak daty rozpoczęcia';
    }

    public function getIsDataZakonczeniaAttribute($value): bool
    {
        return $this->ENDDATE ? true : false;
    }

    public function getDataZakonczeniaAttribute($value): Carbon
    {
        return Carbon::parse($this->ENDDATE);
    }

    public function getDataZakonczeniaFormattedAttribute($value): string
    {
        return $this->is_data_zakonczenia ? $this->data_zakonczenia->format('Y-m-d H:i') : 'Brak daty zakończenia';
    }

    public function getIsTerminAttribute(): bool
    {
        return $this->is_data_rozpoczecia;
    }

    // ========== //

    public function getGodzinaRozpoczeciaAttribute($value): string
    {
        return $this->is_data_rozpoczecia ? $this->data_rozpoczecia->format('H:i') : 'Brak godziny rozpoczęcia';
    }

    public function getGodzinaZakonczeniaAttribute($value): string
    {
        return $this->is_data_zakonczenia ? $this->data_zakonczenia->format('H:i') : 'Brak godziny zakończenia';
    }
}
