<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Zlecenie extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_Zlecenia';
    protected $primaryKey = 'id_zlecenia';
    protected $with = ['kosztorys_opis'];
    public $timestamps = false;

    public static $SYMBOLE_KOSZTORYSU = [
        'ROBOCIZNY' => ['MICHAL-R' => ['Michał'], 'FILIP-R' => ['Filip'], 'MARCIN-R' => ['Marcin'], 'BOGUS-R' => ['Bogdan'], 'ROBERT-R' => ['Robert']],
        'DOJAZDY' => ['MICHAL-D' => ['Michał'], 'FILIP-D' => ['Filip'], 'MARCIN-D' => ['Marcin'], 'BOGUS-D' => ['Bogdan'], 'ROBERT-D' => ['Robert']],
    ];

    public const ERROR_STR = '*Error*';
    public const ODPLATNE_NAME = 'Odpłatne';
    public static $ZLECENIODAWCY = [
        // NIE EDYTOWAĆ INDEX'ÓW
        'Odpłatne' => ['', 'odplatne', 'odpłatne'],
        'Termet' => ['termet'],
        'Amica' => ['amica', 'amika'],
        'Gorenje' => ['gorenje', 'gorenie'],
        'ERGO Hestia' => ['efficient', 'logisfera', 'logiswera', 'ergo-hestia', 'ergohestia', 'ergo', 'hestia'],
        'Quadra-Net' => ['quadra', 'quadra-net', 'quadranet', 'kuadra', 'kuadra-net', 'kuadranet', 'kładra', 'kładra-net', 'kładranet'],
        'IBC' => ['ibc'],
        'Kromet' => ['kromet', 'kromed', 'cromet', 'cromed'],
        'Kernau' => ['kernau', 'kernał'],
        'Novoterm' => ['novoterm', 'nowoterm'],
        'Deante' => ['deante', 'deande'],
        'Ciarko' => ['ciarko', 'ciarco'],
    ];

    /**
    * Attributes
    *
    */

    public function getDataAttribute(): Carbon
    {
        return $this->data_zakonczenia->startOfDay();
    }

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
                'nazwa' => self::ODPLATNE_NAME,
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
        $znacznik = $array[$this->attributes['Z']] ?? $array['_default'];

        return $znacznik;
    }

    public function getZnacznikFormattedAttribute(): string
    {
        $nazwa = $this->znacznik->nazwa;

        if ($nazwa != self::ODPLATNE_NAME) {
            $nazwa .= '-' . $this->zleceniodawca;
        }

        return $nazwa;
    }

    public function getZleceniodawcaAttribute(): string
    {
        $zleceniodawcy = self::$ZLECENIODAWCY;
        $zleceniodawca_type = strtolower($this->kosztorys_opis->opis ?? '');
        $zleceniodawca = '';

        if ($zleceniodawca_type == '' and $this->znacznik->nazwa != self::ODPLATNE_NAME) {
            return self::ERROR_STR;
        }

        foreach ($zleceniodawcy as $_zleceniodawca => $arr) {
            if (in_array($zleceniodawca_type, $arr)) {
                $zleceniodawca = $_zleceniodawca;
            }
        }

        if ($zleceniodawca == '') {
            return 'Nieznany(' . $zleceniodawca_type . ')';
        }
        return $zleceniodawca;
    }

    public function getNrAttribute(): string
    {
        return $this->attributes['NrZlecenia'];
    }

    public function getNrObcyAttribute(): string
    {
        return $this->attributes['NrObcy'] ?? false;
    }

    public function getNrOrObcyAttribute(): string
    {
        return $this->nr_obcy ?: $this->nr;
    }

    public function getStatusIdAttribute(): int
    {
        return $this->attributes['id_status'] ?? false;
    }

    public function setStatusIdAttribute(int $value): void
    {
        $this->attributes['id_status'] = $value;
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
        return in_array($this->status_id, Status::$ZAKONCZONE_IDS) or $this->archiwalny or $this->anulowany;
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
        return $this->attributes['DataKoniec'] ? true : false;
    }

    public function getDataZakonczeniaAttribute(): Carbon
    {
        return $this->terminarz->is_data_zakonczenia ? $this->terminarz->data_zakonczenia : Carbon::parse($this->attributes['DataKoniec']);
    }

    public function getDataZakonczeniaFormattedAttribute(): String
    {
        return $this->data_zakonczenia->format('Y-m-d H:i');
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

        if ($this->dni_od_zakonczenia > 2 and in_array($this->status_id, [Status::UMOWIONO_ID, Status::GOTOWE_DO_WYJAZDU_ID, Status::NA_WARSZTACIE_ID, Status::NIE_ODBIERA_TEL_ID, Status::PONOWNA_WIZYTA_ID]))
            $array[] = 'Zlecenie niezamknięte';

        return $array;
    }

    public function getIsTerminAttribute(): bool
    {
        return $this->terminarz->is_termin;
    }

    public function getIsRozliczoneAttribute(): bool
    {
        return isset($this->rozliczenie);
    }

    public function getOkresAttribute(): string
    {
        return $this->data_zakonczenia->format('Y-m');
    }

    public function getRobociznyAttribute(): array
    {
        return $this->getCalcKosztorys('ROBOCIZNY');
    }

    public function getDojazdyAttribute(): array
    {
        return $this->getCalcKosztorys('DOJAZDY');
    }

    public function getRobociznyHtmlAttribute(): string
    {
        return $this->getHtmlKosztorys('ROBOCIZNY', $this->robocizny);
    }

    public function getDojazdyHtmlAttribute(): string
    {
        return $this->getHtmlKosztorys('DOJAZDY', $this->dojazdy);
    }

    public function getTableCellStatusHTMLAttribute(): string
    {
        $status_table_color = $this->status->color ? 'table-' . $this->status->color : '';
        $status_text_color = $this->status->color ? 'text-' . $this->status->color : '';
        $status_nazwa = str_replace(' ', ' ', $this->status->nazwa); // &nbsp;

        return <<<HTML
            <td class="{$status_table_color}" nowrap>
                <i class="{$this->status->icon} {$status_text_color} mx-2"></i>
                <span class="mr-2">{$status_nazwa}</span>
            </td>
HTML;
    }

    public function getTableCellNrHTMLAttribute(): string
    {
        $route_zleceniaPokaz = route('zlecenia.pokaz', $this->id);

        return <<<HTML
            <td class="font-w600">
                <a href="javascript:void(0)" onclick="PopupCenter('{$route_zleceniaPokaz}', 'zlecenie{$this->id}', 1500, 700)">
                    <i class="{$this->znacznik->icon} mr-2"></i>
                    {$this->nr_or_obcy}
                </a>
                <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="'{$this->nr}'">
                    <i class="far fa-copy"></i>
                </a>
            </td>
HTML;
    }

    // public function getStatusyAttribute()
    // {
    //     return $this->status_historia;
    // }

    /**
    * Scopes
    *
    */

    public function scopeNiezakonczone($query)
    {
        foreach (Status::$ZAKONCZONE_IDS as $status_id) {
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
        return $this->hasOne('App\Models\Zlecenie\Status', 'id_stat', 'id_status')->withDefault([
            'status' => 'Brak statusu',
        ]);
    }

    public function status_historia()
    {
        return $this->hasMany('App\Models\Zlecenie\StatusHistoria', 'id_zs', 'id_zlecenia');
    }

    public function terminarz()
    {
        return $this->hasOne('App\Models\Zlecenie\Terminarz', 'ID_ZLECENIA', 'id_zlecenia')->withDefault([
            'STARTDATE' => false,
            'ENDDATE' => false,
        ]);
    }

    public function urzadzenie()
    {
        return $this->hasOne('App\Models\Zlecenie\Urzadzenie', 'ID_MASZYNY', 'id_maszyny')->withDefault([
            'NAZWA_MASZ' => 'Brak urządzenia',
            'KATEGORIA' => false,
        ]);
    }

    public function kosztorys_pozycje()
    {
        return $this->hasMany('App\Models\Zlecenie\KosztorysPozycja', 'id_zs', 'id_zlecenia');
    }

    public function kosztorys_opis()
    {
        return $this->hasOne('App\Models\Zlecenie\KosztorysOpis', 'id_zs', 'id_zlecenia');
    }

    public function rozliczenie()
    {
        return $this->hasOne('App\Models\Rozliczenie\RozliczoneZlecenie', 'zlecenie_id', 'id_zlecenia');
    }

    /**
    * Scopes
    *
    */

    public function scopeTechnik($query, $technik_id)
    {
        return $query->where('id_o_technika', $technik_id);
    }

    public function scopeWithRelations($query)
    {
        return $query->with('klient', 'status', 'terminarz', 'urzadzenie', 'kosztorys_pozycje', 'rozliczenie');
    }

    /**
    * Methods
    *
    */

    private function getCalcKosztorys(string $type): array
    {
        $symbole_robocizn = self::$SYMBOLE_KOSZTORYSU[$type];
        $array = [];

        foreach ($this->kosztorys_pozycje as $kosztorys_pozycja) {
            $symbol = $kosztorys_pozycja->symbol;
            if (isset($symbole_robocizn[$symbol])) {
                if (!isset($array[$symbol])) $array[$symbol] = 0;
                $ilosc = ($kosztorys_pozycja->ilosc == 0) ? 1 : $kosztorys_pozycja->ilosc;
                $kwota = $kosztorys_pozycja->cena * $ilosc;

                $array[$symbol] += round($kwota, 4);
            }
        }

        return $array;
    }

    public static function getHtmlKosztorys(string $type, array $pozycje): string
    {
        $symbole_pocyzji = self::$SYMBOLE_KOSZTORYSU[$type];
        $str = '';

        foreach ($pozycje as $symbol => $kwota) {
            $pozycja_symbol = $symbole_pocyzji[$symbol];
            $pozycja_imie = $pozycja_symbol[0];

            $str .= '<span class="mr-2"><span class="font-w700">' . $pozycja_imie . '</span>: ' . number_format($kwota, 2, '.', ' ') . ' zł</span> ';
        }

        return $str;
    }

    public function changeStatus(int $status_id, int $pracownik_id, bool $remove_termin = false): void
    {
        $status_historia = new StatusHistoria;
        $status_historia->pracownik_id = $pracownik_id;
        $status_historia->status_id = $status_id;
        $status_historia->data = Carbon::now()->format('Y-m-d H:i:s.000');
        $status_historia->nr_o_zlecenia = null;

        $this->status_historia()->save($status_historia);
        $this->status_id = $status_id;

        if ($this->terminarz()->exists()) {
            $this->terminarz->removeTermin();
            $this->terminarz->save();
        }
    }

    public function appendOpis(string $opis, string $name): void
    {
        $this->opis .= "\r\n** " . $name . " dnia " . date('d.m H:i') . ": „" . $opis . "”";
    }

    public function getNiezakonczone(array $data = [])
    {
        $data = (object) $data;
        $query = $this->withRelations()->niezakonczone()->oldest('DataKoniec');
        if (@$data->technik_id) {
            $query->technik($data->technik_id);
        }
        return $query->get()->sortByDesc('dni_od_zakonczenia');
    }
}
