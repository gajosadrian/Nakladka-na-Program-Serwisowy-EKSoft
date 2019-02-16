<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zlecenie_Status extends Model
{
    protected $table = 'SYSTEM_STATUS';
    protected $primaryKey = 'id_stat';

    public static $ZAKONCZONE_IDS = [26, 29];
    private static $PROPERTIES = [
        11 => [ 'icon' => 'zlec_wpisane', 'color' => false ], 12 => [ 'icon' => 'umowiono_kl', 'color' => false ], 13 => [ 'icon' => 'zamowiono_czesc', 'color' => false ],
        14 => [ 'icon' => 'fa fa-car-side', 'color' => 'danger' ], 16 => [ 'icon' => 'fa fa-home', 'color' => 'warning' ], 17 => [ 'icon' => 'nie_odbiera_tel', 'color' => false ],
        18 => [ 'icon' => 'ponowna_wizyta', 'color' => false ], 25 => [ 'icon' => 'do_wyjasnienia', 'color' => false ], 26 => [ 'icon' => 'zamkniete', 'color' => false ],
        29 => [ 'icon' => 'odwolano', 'color' => false ], 30 => [ 'icon' => 'wniosek_o_wymiane', 'color' => false ], 31 => [ 'icon' => 'fa fa-flag', 'color' => 'danger' ],
        32 => [ 'icon' => 'upierdliwy_kl', 'color' => false ], 33 => [ 'icon' => 'nie_obslugiwac', 'color' => false ], 34 => [ 'icon' => 'zaliczka', 'color' => false ],
        35 => [ 'icon' => 'uzupelnienie_danych', 'color' => false ], 36 => [ 'icon' => 'czesci_do_wyslania', 'color' => false ], 37 => [ 'icon' => 'fa fa-calculator', 'color' => 'danger' ],
        38 => [ 'icon' => 'fa fa-bubbles', 'color' => 'danger' ], 39 => [ 'icon' => 'do_zamowienia', 'color' => false ], 40 => [ 'icon' => 'do_wyceny', 'color' => false ],
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
