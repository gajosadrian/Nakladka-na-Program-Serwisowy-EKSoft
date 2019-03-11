<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SYSTEM_STATUS';
    protected $primaryKey = 'id_stat';

    public const ZLECENIE_WPISANE_ID = 11; public const UMOWIONO_ID = 12; public const ZAMOWIONO_CZESC_ID = 13; public const GOTOWE_DO_WYJAZDU_ID = 14;
    public const NA_WARSZTACIE_ID = 16; public const NIE_ODBIERA_TEL_ID = 17; public const PONOWNA_WIZYTA_ID = 18; public const DO_WYJASNIENIA_ID = 25;
    public const ZAKONCZONE_ID = 26; public const ODWOLANE_ID = 29; public const WNIOSEK_O_WYMIANE_ID = 30; public const DO_ODBIORU_ID = 31;
    public const UPIERDLIWY_KL_ID = 32; public const NIE_OBSLUGIWAC_ID = 33; public const ZALICZKA_ID = 34; public const UZUPELNIENIE_DANYCH_ID = 35;
    public const CZESCI_DO_WYSLANIA_ID = 36; public const DO_ROZLICZENIA_ID = 37; public const INFO_O_KOSZTACH_ID = 38; public const DO_ZAMOWIENIA_ID = 39;
    public const DO_WYCENY_ID = 40; public const PREAUTORYZACJA_ID = 41; public const DO_POINFORMOWANIA_ID = 42;
    public static $ZAKONCZONE_IDS = [26, 29];
    private static $PROPERTIES = [
        11 => [ 'icon' => 'zlec_wpisane', 'color' => false ], 12 => [ 'icon' => 'fa fa-calendar-check', 'color' => 'danger' ], 13 => [ 'icon' => 'fa fa-shopping-cart', 'color' => 'info' ],
        14 => [ 'icon' => 'fa fa-car-side', 'color' => 'danger' ], 16 => [ 'icon' => 'fa fa-home', 'color' => 'warning' ], 17 => [ 'icon' => 'nie_odbiera_tel', 'color' => false ],
        18 => [ 'icon' => 'ponowna_wizyta', 'color' => false ], 25 => [ 'icon' => 'fa fa-exclamation-triangle', 'color' => 'secondary' ], 26 => [ 'icon' => 'fa fa-check-circle', 'color' => 'success' ],
        29 => [ 'icon' => 'fa fa-ban', 'color' => 'secondary' ], 30 => [ 'icon' => 'fa fa-sync-alt', 'color' => 'info' ], 31 => [ 'icon' => 'fa fa-flag', 'color' => 'success' ],
        32 => [ 'icon' => 'upierdliwy_kl', 'color' => false ], 33 => [ 'icon' => 'nie_obslugiwac', 'color' => false ], 34 => [ 'icon' => 'zaliczka', 'color' => false ],
        35 => [ 'icon' => 'uzupelnienie_danych', 'color' => false ], 36 => [ 'icon' => 'czesci_do_wyslania', 'color' => false ], 37 => [ 'icon' => 'fa fa-calculator', 'color' => 'secondary' ],
        38 => [ 'icon' => 'fa fa-comments', 'color' => 'info' ], 39 => [ 'icon' => 'fa fa-cart-plus', 'color' => 'secondary' ], 40 => [ 'icon' => 'fa fa-calculator', 'color' => 'secondary' ],
        41 => [ 'icon' => 'fa fa-question', 'color' => 'danger' ], 42 => [ 'icon' => 'fa fa-phone', 'color' => 'secondary' ],
    ];

    public function getIdAttribute(): int
    {
        return $this->attributes['id_stat'] ?? false;
    }

    public function getNazwaAttribute(): string
    {
        return $this->attributes['status'];
    }

    public function getIconAttribute(): string
    {
        return self::$PROPERTIES[$this->id]['icon'] ?? 'fa fa-question';
    }

    public function getColorAttribute(): string
    {
        return self::$PROPERTIES[$this->id]['color'] ?? 'secondary';
    }

    public static function getName(int $id)
    {
        return self::find($id)->nazwa;
    }

    public static function getIcon(int $id)
    {
        return self::$PROPERTIES[$id]['icon'];
    }

    public static function getColor(int $id)
    {
        return self::$PROPERTIES[$id]['color'];
    }
}