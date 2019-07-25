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

    public const SYMBOLE_KOSZTORYSU = [
        // MOŻNA EDYTOWAĆ IMIONA
        'ROBOCIZNY' => ['SZEF-R' => ['Szef', -1], 'SZYMEK-R' => ['Szymek', 19], 'MICHAL-R' => ['Michał', 2], 'FILIP-R' => ['Filip', 13], 'MARCIN-R' => ['Marcin', 16], 'BOGUS-R' => ['Bogdan', 17], 'ROBERT-R' => ['Robert', 15], 'DAMIAN-R' => ['Damian', 18]],
        'DOJAZDY' => ['SZEF-D' => ['Szef', -1], 'SZYMEK-D' => ['Szymek', 19], 'MICHAL-D' => ['Michał', 2], 'FILIP-D' => ['Filip', 13], 'MARCIN-D' => ['Marcin', 16], 'BOGUS-D' => ['Bogdan', 17], 'ROBERT-D' => ['Robert', 15], 'DAMIAN-D' => ['Damian', 18]],
    ];

    public const ERROR_STR = '*Error*';
    public const ODPLATNE_NAME = 'Odpłatne';
    public const GWARANCJA_NAME = 'Gwarancja';
    public const SPRZEDAZ_CZESCI_NAME = 'Sprzedaż części';
	public const MONTAZ_URZADZENIA_NAME = 'Montaż urządzenia';
    public const ZLECENIODAWCY = [
        // NIE EDYTOWAĆ INDEX'ÓW
        'Odpłatne' => ['', 'odplatne', 'odpłatne', 'odplatnie', 'odpłatnie'],
        'Termet' => ['termet'],
        'Amica' => ['amica', 'amika'],
        'Gorenje' => ['gorenje', 'gorenie', 'gorenja', 'gorenia'],
        'ERGO Hestia' => ['efficient', 'logisfera', 'logiswera', 'ergo-hestia', 'ergohestia', 'ergo', 'hestia', 'ergo hestia'],
        'Quadra-Net' => ['quadra', 'quadra-net', 'quadra net', 'quadra - net', 'quadranet', 'kuadra', 'kuadra-net', 'kuadranet', 'kładra', 'kładra-net', 'kładranet'],
        'IBC' => ['ibc'],
        'Kromet' => ['kromet', 'kromed', 'cromet', 'cromed'],
        'Kernau' => ['kernau', 'kernał', 'galicja'],
        'Novoterm' => ['novoterm', 'nowoterm'],
        'Deante' => ['deante', 'deande'],
        'Ciarko' => ['ciarko', 'ciarco'],
        'Candy' => ['candy', 'candi', 'kandy', 'kandi', 'cendy', 'cendi', 'kendy', 'kendi'],
        'Europ Assistance' => ['europ assistance', 'europ-assistance', 'europ', 'eap'],
        'RTV Euro AGD' => ['euro-net', 'euronet', 'euro', 'rtveuroagd', 'rtv euro agd'],
        'Mentax' => ['mentax', 'mentaks'],
        'De Dietrich' => ['de dietrich', 'dietrich', 'dedietrich'],
        'Arconet' => ['arconet', 'arco net', 'arco-net', 'arkonet', 'arqonet'],
        'Ferroli' => ['ferroli', 'feroli'],
        'Mondial' => ['mondial', 'mondial assistance'],
        'Enpol' => ['enpol'],
        'Akpo' => ['akpo'],
        'Ferro' => ['ferro', 'fero'],
        'Solgaz' => ['solgaz', 'solgas'],
        'Kospel' => ['kospel'],
        'Haier' => ['haier', 'hajer'],
        'Formaster' => ['formaster'],
        'MPM' => ['mpm'],
        'Euroterm' => ['euroterm'],
        'Agdom' => ['agdom'],
        'Makiano' => ['makiano'],
		'STIEBEL ELTRON' => ['stiebel eltron', 'stiebel', 'eltron'],
		'Euro-Serwis 24' => ['euro serwis24', 'euro serwis 24', 'euro serwis', 'euroserwis', 'euroserwis24', 'euro-serwis', 'euro-serwis24', 'euro-serwis 24'],
        'Ravanson' => ['ravanson'],
		'Eldom' => ['eldom'],
    ];

    /**
    * Attributes
    *
    */

    public function getDataAttribute(): Carbon
    {
        return $this->data_zakonczenia->startOfDay();
    }

    public function getIdAttribute(): int
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
                'icon' => 'fa fa-phone',
            ],
            3 => (object) [
                'nazwa' => 'WWW',
                'icon' => 'fa fa-globe',
            ],
            4 => (object) [
                'nazwa' => 'Osobiście',
                'icon' => 'fa fa-user-edit',
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
                'nazwa' => self::GWARANCJA_NAME,
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
            'G' => (object) [
                'nazwa' => 'NKS',
                'icon' => 'fa fa-sync-alt',
                'color' => false,
            ],
            'D' => (object) [
                'nazwa' => self::SPRZEDAZ_CZESCI_NAME,
                'icon' => 'fa fa-shopping-cart',
                'color' => false,
            ],
            'E' => (object) [
                'nazwa' => self::MONTAZ_URZADZENIA_NAME,
                'icon' => 'fa fa-dollar-sign',
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
        $zleceniodawcy = self::ZLECENIODAWCY;
        $zleceniodawca_type = trim(strtolower($this->kosztorys_opis->opis ?? ''));
        $zleceniodawca = '';

        if ($zleceniodawca_type == '' and !in_array($this->znacznik->nazwa, [self::ODPLATNE_NAME, self::SPRZEDAZ_CZESCI_NAME, self::MONTAZ_URZADZENIA_NAME])) {
            if ($this->znacznik->nazwa == self::GWARANCJA_NAME) {
                $zleceniodawca_type = strtolower($this->urzadzenie->producent);
            } else {
                return self::ERROR_STR;
            }
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

    public function getZleceniodawcaFormattedAttribute(): string
    {
        if ($this->zleceniodawca != self::ERROR_STR) {
            return $this->zleceniodawca;
        }

        return '<span class="font-w700 text-danger">' . $this->zleceniodawca . '</span>';
    }

    public function getNrAttribute(): string
    {
        return $this->attributes['NrZlecenia'] ?? false;
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

    public function getIsOdplatneAttribute(): bool
    {
        return $this->zleceniodawca == self::ODPLATNE_NAME;
    }

    public function getIsOdwolanoAttribute(): bool
    {
        return $this->anulowany or $this->status_id == Status::ODWOLANO_ID;
    }

    public function getIsZakonczoneAttribute(): bool
    {
        return in_array($this->status_id, Status::ZAKONCZONE_IDS) or $this->archiwalny or $this->anulowany;
    }

    public function getIsUmowionoAttribute(): bool
    {
        return ($this->status_id == Status::UMOWIONO_ID);
    }

    public function getIsWarsztatAttribute(): bool
    {
        return ($this->status_id == Status::NA_WARSZTACIE_ID);
    }

    public function getWasWarsztatAttribute(): bool
    {
        foreach ($this->statusy as $status) {
            if ($status->status_id == Status::NA_WARSZTACIE_ID) {
                return true;
            } elseif (in_array($status->status_id, [Status::GOTOWE_DO_WYJAZDU_ID, Status::UMOWIONO_ID])) {
                return false;
            }
        }
        return false;
    }

    public function getIsGotoweAttribute(): bool
    {
        return ($this->status_id == Status::GOTOWE_DO_WYJAZDU_ID);
    }

    public function getLastStatusNieOdbieraAttribute(): ?object
    {
        foreach ($this->statusy as $status) {
            if ($status->status_id == Status::NIE_ODBIERA_ID) {
                return $status;
            }
        }
        return null;
    }

    public function getIsRecentlyNieOdbieraAttribute(): bool
    {
        $status = $this->last_status_nie_odbiera;
        if (! $status) return false;
        if ($status->data->gte( now()->copy()->subHours(1) )) {
            return true;
        }
        return false;
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

    public function getDataStatusuAttribute(): Carbon
    {
        return $this->statusy->first()->data;
    }

    public function getDataStatusuFormattedAttribute(): String
    {
        return $this->data_statusu->format('Y-m-d H:i');
    }

    public function getIsAkcKosztowAttribute(): bool
    {
        return $this->attributes['data_akc_koszt'] ? true : false;
    }

    public function getDataAkcKosztowAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['data_akc_koszt']);
    }

    public function getOpisAttribute(): string
    {
        return $this->attributes['OpisZlec'] ?? false;
    }

    public function getOpisFormattedAttribute(): string
    {
        $opis = $this->opis;
        $opis = strtr($opis, [
            '>> ' => '>>',
            ' <<' => '<<',
        ]);
        $opis = strtr($opis, [
            '>>' => '<span class="bg-gray font-w700 px-1">',
            '<<' => '</span>',
        ]);
        $opis = str_replace("\n", '<br>', $opis);
        return $opis;
    }

    public function setOpisAttribute(string $value): void
    {
        $this->attributes['OpisZlec'] = $value;
    }

    public function getOpisBrAttribute(): string
    {
        return nl2br($this->opis);
    }

    public function getDniOdStatusuAttribute(): int
    {
        return $this->statusy->first()->data->copy()->startOfDay()->diffInDays(now()->startOfDay(), false);
    }

    public function getDniOdZakonczeniaAttribute(): int
    {
        return $this->data_zakonczenia->copy()->startOfDay()->diffInDays(now()->startOfDay(), false);
    }

    public function getDniOdPrzyjeciaAttribute(): int
    {
        return $this->data_przyjecia->diffInDays(now()->endOfDay(), false);
    }

    public function getCzasTrwaniaAttribute(): int
    {
        $data = now();
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

        if (!$this->is_termin and in_array($this->status_id, [Status::GOTOWE_DO_WYJAZDU_ID, Status::NIE_ODBIERA_ID, Status::PONOWNA_WIZYTA_ID, Status::ZLECENIE_WPISANE_ID]))
            $array[] = 'Ustal termin';

        if ($this->dni_od_zakonczenia >= 2 and $this->isAktywnyBlad(2) and in_array($this->status_id, [Status::UMOWIONO_ID, Status::GOTOWE_DO_WYJAZDU_ID, Status::NA_WARSZTACIE_ID, Status::NIE_ODBIERA_ID, Status::PONOWNA_WIZYTA_ID]))
            $array[] = 'Zlecenie niezamknięte';

        if (!$this->is_termin and $this->dni_od_statusu >= 2 and $this->isAktywnyBlad(2) and in_array($this->status_id, [Status::NA_WARSZTACIE_ID]))
            $array[] = 'Zlecenie niezamknięte';

        if ($this->dni_od_statusu >= 1 and $this->isAktywnyBlad(1) and in_array($this->status_id, [Status::INFO_O_KOSZTACH_ID]))
            $array[] = 'Dzwonić do klienta';

        if ($this->dni_od_statusu >= 1 and $this->isAktywnyBlad(1) and in_array($this->status_id, [Status::DO_ZAMOWIENIA_ID, Status::DO_WYCENY_ID, Status::DO_WYJASNIENIA_ID]))
            $array[] = 'Brak reakcji';

        if ($this->dni_od_statusu >= 3 and $this->isAktywnyBlad(3) and in_array($this->status_id, [Status::ZALICZKA_ID]))
            $array[] = 'Czy jest zaliczka?';

        if ($this->dni_od_statusu >= 1 and $this->isAktywnyBlad(1) and in_array($this->status_id, [Status::DZWONIC_PO_ODBIOR_ID, Status::DO_ODBIORU_ID]))
            $array[] = 'Dzwonić po odbiór';

        if ($this->dni_od_statusu >= 5 and $this->isAktywnyBlad(5) and in_array($this->status_id, [Status::DO_ROZLICZENIA_ID]))
            $array[] = 'Nie rozliczone';

        if ($this->dni_od_statusu >= 4 and $this->isAktywnyBlad(4) and in_array($this->status_id, [Status::ZAMOWIONO_CZESC_ID]))
            $array[] = 'Czy dotarła część?';

        return $array;
    }

    public function getSamochodAttribute()
    {
        if (! $this->is_termin) return false;
        return $this->terminarz->samochod;
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

    public function getSumaRobociznAttribute(): float
    {
        return array_sum($this->robocizny);
    }

    public function getDojazdyAttribute(): array
    {
        return $this->getCalcKosztorys('DOJAZDY');
    }

    public function getSumaDojazdowAttribute(): float
    {
        return array_sum($this->dojazdy);
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
        $copy_nr = $this->nr_obcy ?: $this->nr;

        return <<<HTML
            <td class="font-w600" nowrap>
				{$this->znacznik_formatted}<br>
                <a href="javascript:void(0)" onclick="{$this->popup_link}">
                    <i class="{$this->znacznik->icon} mr-1"></i>
                    {$this->nr_or_obcy}
                </a>
                <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="'{$copy_nr}'">
                    <i class="far fa-copy"></i>
                </a>
            </td>
HTML;
    }

    public function getPopupLinkAttribute(): string
    {
        return "PopupCenter('" . route('zlecenia.pokaz', $this->id) . "', 'zlecenie" . $this->id . "', 1500, 800)";
    }

    public function getStatusyAttribute()
    {
        return $this->status_historia->sortByDesc('data');
    }

    /**
    * Scopes
    *
    */

    public function scopeNiezakonczone($query)
    {
        foreach (Status::ZAKONCZONE_IDS as $status_id) {
            $query->where('id_status', '!=', $status_id);
        }
        return $query->where('Archiwalny', false)->where('Anulowany', null);
    }

    public function scopeZakonczone($query)
    {
        return $query->where(function ($subquery) {
            $subquery->where('id_status', Status::ZAKONCZONE_ID)->orWhere('id_status', Status::DO_ROZLICZENIA_ID);
        })->where('Anulowany', null);
    }

    public function scopeTechnik($query, $technik_id)
    {
        return $query->where('id_o_technika', $technik_id);
    }

    public function scopeWithRelations($query)
    {
        return $query->with('klient', 'status', 'terminarz', 'urzadzenie', 'kosztorys_pozycje', 'rozliczenie');
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
        return $this->belongsTo('App\Models\Zlecenie\Terminarz', 'id_zlecenia', 'ID_ZLECENIA')->withDefault([
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

    public function zatwierdzony_blad()
    {
        return $this->hasOne('App\Models\Zlecenie\ZatwierdzonyBlad', 'zlecenie_id', 'id_zlecenia');
    }

    /**
    * Methods
    *
    */

    public function isAktywnyBlad(int $dni): bool
    {
        if (! $this->zatwierdzony_blad) return true;
        if ($this->zatwierdzony_blad->status_id != $this->status_id) return true;
        if ($this->zatwierdzony_blad->created_at->copy()->startOfDay()->gt(now()->startOfDay()->subDays($dni))) {
            return false;
        }
        return true;
    }

    private function getCalcKosztorys(string $type): array
    {
        $symbole_robocizn = self::SYMBOLE_KOSZTORYSU[$type];
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
        $symbole_pocyzji = self::SYMBOLE_KOSZTORYSU[$type];
        $str = '';

        foreach ($pozycje as $symbol => $kwota) {
            $pozycja_symbol = $symbole_pocyzji[$symbol];
            $pozycja_imie = $pozycja_symbol[0];

            $str .= '<span class="mr-2"><span class="font-w700">' . $pozycja_imie . '</span>: ' . round($kwota, 2) . ' zł</span> ';
        }

        return $str;
    }

    // public function getSumOf(string $type, string $pozycja_type = null): int
    // {
    //     $type_lower = strtolower($type);
    //     $pozycje = $this->type_lower;
    //
    //     return self::helper_getSumOf($pozycje);
    // }
    //
    // public static function helper_getSumOf(array $pozycje, string $pozycja_type = null): int
    // {
    //     if (!isset($pozycja_type)) {
    //         return array_sum($pozycje);
    //     }
    //
    //     $sum = 0;
    //     foreach ($pozycje as $pozycja_symbol => $kwota) {
    //         if ($pozycja_symbol == $pozycja_type) {
    //             $sum += $kwota;
    //         }
    //     }
    //     return $sum;
    // }

    public function changeStatus(int $status_id, int $pracownik_id, bool $remove_termin = false): void
    {
        $status_historia = new StatusHistoria;
        $status_historia->pracownik_id = $pracownik_id;
        $status_historia->status_id = $status_id;
        $status_historia->data = now()->format('Y-m-d H:i:s.000');
        $status_historia->nr_o_zlecenia = null;

        $this->status_historia()->save($status_historia);
        $this->status_id = $status_id;

        if ($remove_termin and $this->terminarz()->exists()) {
            $this->terminarz->removeTermin();
            $this->terminarz->save();
        }
    }

    public function appendOpis(string $opis, string $name, bool $minified = false): void
    {
        if (! $minified) {
            $opis = preg_replace("/(\r?\n)/", ' ', $opis);
            $opis = str_replace([' .', ' ,'], ['. ', ', '], $opis);
            $this->opis .= "\r\n# " . $name . ' ' . date('d.m H:i') . ':  ' . $opis;
        } else {
            // TODO: przerobić funkcję
            $user = auth()->user();
            $this->opis .= "\r\n" . date('d.m H:i') . ' ' . $user->short_name . ': ' . $opis;
        }
    }

    public function getNiezakonczone(array $data = [])
    {
        $data = (object) $data;
        $query = $this->withRelations()->with(['status_historia', 'zatwierdzony_blad'])->niezakonczone()->oldest('DataKoniec');
        if (@$data->technik_id) {
            $query->technik($data->technik_id);
        }
        return $query->get()->sortByDesc('dni_od_zakonczenia');
    }

    public static function getDoRozliczenia()
    {
        return $query = self::with('status', 'terminarz', 'kosztorys_pozycje', 'rozliczenie')->zakonczone()->latest('id_zlecenia')->limit(2000)->get()
            ->filter(function ($zlecenie) {
                // return !$zlecenie->is_rozliczone and $zlecenie->data_zakonczenia <= Carbon::create(2019, 1, 31)->endOfDay() and $zlecenie->status->id == 26;
                return !$zlecenie->is_rozliczone;
            })->sortBy('data_zakonczenia');
    }
}
