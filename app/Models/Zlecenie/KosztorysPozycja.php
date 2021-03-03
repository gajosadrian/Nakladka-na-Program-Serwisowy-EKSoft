<?php

namespace App\Models\Zlecenie;

// use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Database\Eloquent\Model;

class KosztorysPozycja extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'EKSOFT_GT.dbo.ser_ZlecKosztPoz';
    protected $with = ['towar'];
    protected $guarded = ['id'];
    public $timestamps = false;

    private const
        ZAMONTOWANE_KEYS = ['zamontowan', 'zalozon'],
        NIEZAMONTOWANE_KEYS = ['niezamontowan', 'niezalozon', 'niepotrzeb'],
        PRZELOZYC_KEYS = ['__'],
        ODLOZONE_KEYS = ['odlozon'],
        ROZPISANE_KEYS = ['rozpisan'],
        ZAMOWIONE_KEYS = ['zamowion', 'zamowien'],
        WYCENA_KEYS = ['wycena', 'wycenion'],
        DEPOZYT_KEYS = ['depozyt'],
        EKSPERTYZA_KEYS = ['ekspertyza'];

    /**
     * Attributes
     *
     */

    public function getTowarIdAttribute(): int
    {
        return $this->attributes['id_o_tw'];
    }

    public function setTowarIdAttribute(int $towar_id): void
    {
        $this->attributes['id_o_tw'] = $towar_id;
    }

    public function getZlecenieIdAttribute(): int
    {
        return $this->attributes['id_zs'];
    }

    public function setZlecenieIdAttribute(int $zlecenie_id): void
    {
        $this->attributes['id_zs'] = $zlecenie_id;
    }

    public function setZlecenieNrAttribute(string $zlecenie_nr): void
    {
        $this->attributes['nr_o_zlecenia'] = $zlecenie_nr;
    }

    public function getNazwaAttribute(): string
    {
        return $this->towar->nazwa;
    }

    public function getSymbolAttribute(): string
    {
        return $this->towar->symbol;
    }

    public function getSymbolDostawcyAttribute(): string
    {
        $str = $this->zamiennik ?: $this->towar->symbol_dostawcy;
        if ($this->zamiennik) {
            $str = '*' . $str;
        }
        return $str;
    }

    public function getSymbolDostawcy2Attribute(): string
    {
        return $this->towar->symbol_dostawcy2;
    }

    public function getJednostkaAttribute(): string
    {
        return $this->attributes['jm'] ?? false;
    }

    public function setJednostkaAttribute(string $jednostka): void
    {
        $this->attributes['jm'] = $jednostka;
    }

    public function getOpisRawAttribute(): string
    {
        return $this->attributes['opis_dodatkowy'] ?? false;
    }

	public function getOpisAttribute(): string
    {
        $opis = $this->attributes['opis_dodatkowy'] ?? false;
        if ( ! $opis) {
            return false;
        }
        $opis = trim(str_replace('$'.$this->naszykowana_czesc_key.'$', '', $opis));
        return $opis;
    }

	public function setOpisAttribute(string $value): void
    {
        $this->attributes['opis_dodatkowy'] = trim($value);
    }

	public function getOpisFixedAttribute(): string
    {
        $opis = $this->opis;
        if ($opis == '') return false;
        $opis = preg_replace("/\[[^)]+\]/", '', $opis);
        return trim($opis);
    }

    public function getOpisAsciiAttribute(): string
    {
        return strtolower(str_replace("'", '', iconv('UTF-8', 'ascii//translit', $this->opis)));
    }

    public function getCenaAttribute(): float
    {
        return round($this->attributes['cena'], 4);
    }

    public function setCenaAttribute(float $cena): void
    {
        $this->attributes['cena'] = $cena;
    }

    public function getCenaFormattedAttribute(): string
    {
        return number_format($this->cena, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getCenaBruttoAttribute(): float
    {
        return self::getFixedValue(round($this->cena * ($this->vat + 1), 4));
    }

	public function getCenaBruttoFormattedAttribute(): string
    {
        return number_format($this->cena_brutto, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getIloscAttribute(): float
    {
        return round($this->attributes['ilosc'], 2);
    }

    public function setIloscAttribute(float $value): void
    {
        $this->attributes['ilosc'] = $value;
    }

    public function getWartoscAttribute(): float
    {
        return round($this->cena * $this->ilosc, 4);
    }

    public function getWartoscFormattedAttribute(): string
    {
        return number_format($this->wartosc, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getWartoscBruttoAttribute(): float
    {
        return self::getFixedValue(round($this->wartosc * ($this->vat + 1), 4));
    }

    public function getWartoscBruttoFormattedAttribute(): string
    {
        return number_format($this->wartosc_brutto, 2, '.', ' ') . ' zł'; // &nbsp;
    }

    public function getVatProcentAttribute(): int
    {
        return $this->vat * 100;
    }

    public function getIsCzescAttribute(): bool
    {
        return is_numeric($this->symbol) or $this->opis;
    }

    public function getIsTowarAttribute(): bool
    {
        return $this->towar->rodzaj == 1;
    }

    public function getIsUslugaAttribute(): bool
    {
        return $this->towar->rodzaj == 2;
    }

    public function getPolkaAttribute(): string
    {
        return $this->towar->polka;
    }

    public function getZamiennikAttribute(): string
    {
        preg_match('#\[(.*?)\]#', $this->opis, $match);
        if (@isset( $match[1] )) {
            return $match[1];
        }
        return false;
    }

    public function getIsZdjecieAttribute(): bool
    {
        return $this->towar->is_zdjecie;
    }

    public function getZdjecieUrlAttribute(): string
    {
        return $this->towar->zdjecie_url;
    }

    public function getIsCzescSymbolAttribute(): bool
    {
        return $this->symbol == 'CZĘŚĆ';
    }

    public function getIsZamontowaneAttribute(): bool
    {
        return $this->hasKey(self::ZAMONTOWANE_KEYS) && !$this->hasKey(self::NIEZAMONTOWANE_KEYS);
    }

    public function getIsNiezamontowaneAttribute(): bool
    {
        return $this->hasKey(self::NIEZAMONTOWANE_KEYS);
    }

    public function getIsPrzelozycAttribute(): bool
    {
        return $this->hasKey(self::PRZELOZYC_KEYS);
    }

    public function getIsOdlozoneAttribute(): bool
    {
        return $this->hasKey(self::ODLOZONE_KEYS);
    }

    public function getIsRozpisaneAttribute(): bool
    {
        return $this->hasKey(self::ROZPISANE_KEYS);
    }

    public function getIsZamowioneAttribute(): bool
    {
        return $this->hasKey(self::ZAMOWIONE_KEYS);
    }

    public function getIsWycenaAttribute(): bool
    {
        return $this->hasKey(self::WYCENA_KEYS);
    }

    public function getIsDepozytAttribute(): bool
    {
        return $this->hasKey(self::DEPOZYT_KEYS);
    }

    public function getIsEkspertyzaAttribute(): bool
    {
        return $this->hasKey(self::EKSPERTYZA_KEYS);
    }

    public function getStateFormattedAttribute(): string
    {
        if ($this->is_zamontowane) {
            return 'Zamontowane';
        } elseif ($this->is_odlozone) {
            return 'Odłożone';
        } elseif ($this->is_rozpisane) {
            return 'Rozpisane';
        } elseif ($this->is_zamowione) {
            return 'Zamówione';
        } elseif ($this->is_niezamontowane) {
            return 'Niepotrzebne';
        } elseif ($this->is_wycena) {
            return 'Wycena';
        } elseif ($this->is_depozyt) {
            return 'Depozyt';
        } elseif (!$this->is_czesc_symbol and $this->opis) {
            return $this->opis;
        }
        return '-';
    }

    public function getNaszykowanaCzescKeyAttribute(): ?string
    {
        $opis = $this->opis_raw;
        if ( ! str_contains2($opis, '$')) return null;
        return get_string_between($opis, '$', '$');
    }

    public function setNaszykowanaCzescKeyAttribute(string $key): void
    {
        $opis = $this->opis;
        $old_key = $this->naszykowana_czesc_key;

        if ($old_key) {
            $opis = trim(str_replace('$'.$old_key.'$', '', $opis));
        }
        if ( ! $key) {
            return;
        }

        $this->opis = $opis . ' $' . $key . '$';
    }

    public function getNaszykowanaCzescAttribute()
    {
        if (!$this->naszykowana_czesc_key or !$this->naszykowane_czesci) return null;
        return $this->naszykowane_czesci->where('key', $this->naszykowana_czesc_key)->first() ?? null;
    }

    /**
     * Methods
     *
     */

    public function hasKey(array $keys): bool
    {
        return str_contains2($this->opis_ascii, $keys);
    }

    public function changeOpis(?string $opis): void
    {
        $user = auth()->user();
        $key = $this->naszykowana_czesc_key;

        $this->opis = $opis ?? '';
        if ($key) {
            $this->naszykowana_czesc_key = $key;
        }
    }

    /**
     * Relations
     *
     */

    public function zlecenie()
    {
        return $this->belongsTo('App\Models\Zlecenie\Zlecenie', 'id_zs', 'id_zlecenia');
    }

    public function towar()
    {
        return $this->hasOne('App\Models\Subiekt\Subiekt_Towar', 'tw_Id', 'id_o_tw')->withDefault([
            'tw_Nazwa' => '-',
            'tw_Opis' => '',
            'tw_Symbol' => '-',
            'tw_DostSymbol' => '',
            'tw_PKWiU' => '',
        ]);
    }

    public function naszykowane_czesci()
    {
        return $this->hasMany('App\Models\Czesc\Naszykowana', ['zlecenie_id', 'towar_id'], ['id_zs', 'id_o_tw']);
    }

    /**
     * Methods
     *
     */

    public function getArray(): array
    {
        $array = [
            'id' => $this->id,
            'polka' => $this->polka,
            'symbol_dostawcy' => $this->symbol_dostawcy,
            'symbol' => $this->symbol,
            'nazwa' => $this->nazwa,
            'opis' => $this->opis_fixed,
            'cena' => $this->cena,
            'cena_brutto' => $this->cena_brutto,
            'ilosc' => $this->ilosc,
            'wartosc' => $this->wartosc,
            'wartosc_brutto' => $this->wartosc_brutto,
            'vat_procent' => $this->vat_procent,
            'is_czesc' => $this->is_czesc,
            'is_ekspertyza' => $this->is_ekspertyza,
            'is_towar' => $this->is_towar,
            'is_usluga' => $this->is_usluga,
            'is_zamowione' => $this->is_zamowione,
            'is_odlozone' => $this->is_odlozone,
            'is_zamontowane' => $this->is_zamontowane,
            'is_przelozyc' => $this->is_przelozyc,
            'is_niezamontowane' => $this->is_niezamontowane,
            'is_rozpisane' => $this->is_rozpisane,
            'is_zamontowane_or_rozpisane' => ($this->is_zamontowane or $this->is_rozpisane),
            'naszykowana_czesc' => false,
        ];
        if ($this->naszykowana_czesc) {
            $array['naszykowana_czesc'] = $this->naszykowana_czesc->only([
                'id',
                'ilosc',
                'ilosc_do_zwrotu',
                'ilosc_zamontowane',
                'ilosc_rozpisane',
            ]);
            $is_editable = false;
            if ($this->naszykowana_czesc->is_naszykowane) {
                if (! $this->naszykowana_czesc->technik_updated_at or $this->naszykowana_czesc->technik_updated_at->isToday()) {
                    $is_editable = true;
                }
            }
            $array['naszykowana_czesc']['is_editable'] = $is_editable;
        }
        return $array;
    }

    public static function getFixedValue($value)
    {
        $value_str = (string) $value;
        if (strpos($value_str, '.') !== false) {
            $digits = str_split(explode('.', $value_str)[1]);
            if (isset($digits[0]) and isset($digits[1]) and $digits[0] == '9' and ($digits[1] == '9' or $digits[1] == '8')) {
                return (int) $value + 1;
            }
        }
        return $value;
    }
}
