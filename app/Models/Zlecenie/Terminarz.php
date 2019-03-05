<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Terminarz extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Terminarz';
    protected $primaryKey = 'ID_TERMINU';
    public $timestamps = false;

    public const BRAK_ID = '0x00000000006C8EAE'; public const TERMIN_USTALONY_ID = '0x00000000006C8EA5';
    public const DZWONIC_WCZESNIEJ_ID = '0x00000000006C8EA6'; public const ZAKONCZONE_ID = '0x00000000006C8EA7';
    public const TERMIN_WSTEPNIE_USTALONY_ID = '0x00000000006C8EA8'; public const DO_ODWIEZIENIA_ID = '0x00000000006C8EA9';
    public const ZAMOWIONO_CZESC_ID = '0x00000000006C8EAA'; public const NA_WARSZTACIE_ID = '0x00000000006C8EAB';

    /**
     * Attributes
     *
     */

     public function getZlecenieIdAttribute(): int
     {
         return $this->ID_ZLECENIA;
     }

     public function setZlecenieIdAttribute(int $value = null): void
     {
         $this->attributes['ID_ZLECENIA'] = $value;
     }

     public function getTypAttribute(): int
     {
         return $this->TS;
     }

     public function setTypAttribute(string $value): void
     {
         $this->attributes['TS'] = $value;
     }

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

    /**
     * Methods
     *
     */

     public function removeTermin(bool $delete = false): void
     {
         if ($delete) {
             $this->delete();
             return;
         }

         $this->zlecenie_id = null;
         // $this->typ = Terminarz::ZAKONCZONE_ID;
     }
}
