<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Terminarz extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Terminarz';
    protected $primaryKey = 'ID_TERMINU';
    public $timestamps = false;

    public const SAMOCHOD_KEYS = ['samochod', 'samochód'];

    public const BRAK_ID = '536870912'; public const UMOWIONO_ID = '8689404';
    public const DZWONIC_WCZESNIEJ_ID = '14982788'; public const ZAKONCZONE_ID = '6610596';
    public const TERMIN_WSTEPNIE_USTALONY_ID = '7649020'; public const DO_ODWIEZIENIA_ID = '16051844';
    public const ZAMOWIONO_CZESC_ID = '16033476'; public const NA_WARSZTACIE_ID = '7661308';

    public const DZWONIC_WCZESNIEJ_STR = 'Dzwonić 30 min wcześniej';

    /**
     * Attributes
     *
     */

     public function getZlecenieIdAttribute(): int
     {
         return $this->attributes['ID_ZLECENIA'];
     }

     public function setZlecenieIdAttribute(int $value = null): void
     {
         $this->attributes['ID_ZLECENIA'] = $value;
     }

     public function getStatusIdAttribute(): string
     {
         return $this->attributes['label'] ?? self::BRAK_ID;
     }

     public function setStatusIdAttribute(string $value): void
     {
         $this->attributes['label'] = $value;
     }

    public function getIsDataRozpoczeciaAttribute($value): bool
    {
        return $this->attributes['STARTDATE'] ? true : false;
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
        return $this->attributes['ENDDATE'] ? true : false;
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

    public function getTechnikIdAttribute(): int
    {
        return $this->attributes['id_techn_term'];
    }

    public function getPrzeznaczonyCzasFormattedAttribute(): string
    {
        $diff = $this->data_zakonczenia->diffInSeconds($this->data_rozpoczecia);
        list($hours, $minutes) = explode(':', gmdate('H:i', $diff));
        return sprintf('%u godz. %u min', $hours, $minutes);
    }

    public function getTematAttribute(): string
    {
        return $this->attributes['temat'] ?? false;
    }

    public function getGodzinaRozpoczeciaAttribute($value): string
    {
        return $this->is_data_rozpoczecia ? $this->data_rozpoczecia->format('H:i') : 'Brak godziny rozpoczęcia';
    }

    public function getGodzinaZakonczeniaAttribute($value): string
    {
        return $this->is_data_zakonczenia ? $this->data_zakonczenia->format('H:i') : 'Brak godziny zakończenia';
    }

    public function getIsSamochodAttribute(): bool
    {
        return Str::contains($this->temat, self::SAMOCHOD_KEYS);
    }

    /**
    * Relations
    *
    */

    public function technik()
    {
        return $this->hasOne('App\Models\SMS\Technik', 'id_technika', 'id_techn_term');
    }

    public function zlecenie()
    {
        return $this->belongsTo('App\Models\Zlecenie\Zlecenie', 'ID_ZLECENIA', 'id_zlecenia')->withDefault([
            'id_zlecenia' => 0,
        ]);
    }

    /**
     * Methods
     *
     */

    public static function getSamochod($technik_id, $date_string)
    {
        $termin_samochod = self::where('STARTDATE', $date_string . ' 00:00:00:000')->where('id_techn_term', $technik_id)->get()
        ->filter(function ($v) {
            return $v->is_samochod;
        })->first();

        if ($termin_samochod) {
            $termin_temat = substr(str_replace(self::SAMOCHOD_KEYS, '', strtolower($termin_samochod->temat)), 1);
            foreach (Zlecenie::SYMBOLE_KOSZTORYSU['DOJAZDY'] as $symbol => $value) {
                if (Str::contains( strtolower($symbol), $termin_temat )) {
                    return [
                        'symbol' => $symbol,
                        'value' => $value,
                    ];
                }
            }
        } else {
            foreach (Zlecenie::SYMBOLE_KOSZTORYSU['DOJAZDY'] as $symbol => $value) {
                if ($value[1] == $technik_id) {
                    return [
                        'symbol' => $symbol,
                        'value' => $value,
                    ];
                }
            }
        }
    }

    public function removeTermin(bool $delete = false): void
    {
        if ($delete) {
            $this->delete();
            return;
        }

        $this->zlecenie_id = null;
        $this->status_id = Terminarz::ZAKONCZONE_ID;
    }
}
