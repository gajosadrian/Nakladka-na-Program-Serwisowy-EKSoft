<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zlecenie_Status extends Model
{
    protected $table = 'SYSTEM_STATUS';
    protected $infoKey = 'id_stat';

    public static $ZAKONCZONE_IDS = [26, 29];
    private static $PROPERTIES = [
        11 => [ 'icon' => 'zlec_wpisane', 'color' => false ], 12 => [ 'icon' => 'fa fa-calendar-check', 'color' => 'danger' ], 13 => [ 'icon' => 'fa fa-shopping-cart', 'color' => 'info' ],
        14 => [ 'icon' => 'fa fa-car-side', 'color' => 'danger' ], 16 => [ 'icon' => 'fa fa-home', 'color' => 'warning' ], 17 => [ 'icon' => 'nie_odbiera_tel', 'color' => false ],
        18 => [ 'icon' => 'ponowna_wizyta', 'color' => false ], 25 => [ 'icon' => 'fa fa-exclamation-triangle', 'color' => 'danger' ], 26 => [ 'icon' => 'fa fa-check-circle', 'color' => 'success' ],
        29 => [ 'icon' => 'fa fa-ban', 'color' => 'secondary' ], 30 => [ 'icon' => 'fa fa-sync-alt', 'color' => 'info' ], 31 => [ 'icon' => 'fa fa-flag', 'color' => 'warning' ],
        32 => [ 'icon' => 'upierdliwy_kl', 'color' => false ], 33 => [ 'icon' => 'nie_obslugiwac', 'color' => false ], 34 => [ 'icon' => 'zaliczka', 'color' => false ],
        35 => [ 'icon' => 'uzupelnienie_danych', 'color' => false ], 36 => [ 'icon' => 'czesci_do_wyslania', 'color' => false ], 37 => [ 'icon' => 'fa fa-calculator', 'color' => 'danger' ],
        38 => [ 'icon' => 'fa fa-comments', 'color' => 'info' ], 39 => [ 'icon' => 'do_zamowienia', 'color' => false ], 40 => [ 'icon' => 'do_wyceny', 'color' => false ],
    ];

    public function getIdAttribute(): int
    {
        return $this->attributes['id_stat'];
    }

    public function getNazwaAttribute(): string
    {
        return $this->attributes['status'];
    }

    public function getIconAttribute(): string
    {
        return self::$PROPERTIES[$this->id]['icon'];
    }

    public function getColorAttribute(): string
    {
        return self::$PROPERTIES[$this->id]['color'];
    }
}
