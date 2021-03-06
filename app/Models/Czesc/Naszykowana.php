<?php

namespace App\Models\Czesc;

// use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Database\Eloquent\Model;

class Naszykowana extends Model
{
    protected $connection = 'mysql';
    protected $table = 'naszykowane_czesci';
    protected $dates = ['zlecenie_data', 'sprawdzone_at', 'technik_updated_at'];
    protected $guarded = [];

    /**
     * Attributes
     *
     */

    public function getIloscZwroconeAttribute(): float
    {
        return $this->ilosc - $this->ilosc_zamontowane - $this->ilosc_do_zwrotu;
    }

    public function getIsZamontowaneAttribute(): bool
    {
        return $this->ilosc_zamontowane > 0;
    }

    public function getIsRozpisaneAttribute(): bool
    {
        return $this->ilosc_rozpisane > 0;
    }

    public function getIsRozliczoneAttribute(): bool
    {
        return $this->is_zamontowane or $this->is_rozpisane;
    }

    public function getIsNaszykowaneAttribute(): bool
    {
        return ! $this->sprawdzone_at;
    }

    public function getTechnikUpdatedAtFormattedAttribute(): string
    {
        return $this->technik_updated_at->format('Y-m-d H:i');
    }

    public function getZlecenieDataFormattedAttribute(): string
    {
        return $this->zlecenie_data->toDateString();
    }

    public function getIsZlecenieDataPastAttribute(): bool
    {
        return $this->zlecenie_data->copy()->endOfDay()->lt( now() );
    }

    public function getKosztorysPozycjaAttribute()
    {
        if ( ! $this->kosztorys_pozycje ) return null;
        return $this->kosztorys_pozycje->where('naszykowana_czesc_key', $this->key)->first();
    }

    /**
     * Relations
     *
     */

    public function kosztorys_pozycje()
    {
        return $this->hasMany('App\Models\Zlecenie\KosztorysPozycja', ['id_zs', 'id_o_tw'], ['zlecenie_id', 'towar_id']);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function user_sprawdzil()
    {
        return $this->belongsTo('App\User', 'sprawdzil_user_id');
    }

    public function zlecenie()
    {
        return $this->belongsTo('App\Models\Zlecenie\Zlecenie', 'zlecenie_id', 'id_zlecenia');
    }

    public function towar()
    {
        return $this->belongsTo('App\Models\Subiekt\Subiekt_Towar', 'towar_id', 'tw_Id');
    }

    /**
     * Static
     *
     */

    static function getNiesprawdzoneCount(): int
    {
        $naszykowane = static::with(['towar', 'zlecenie'])
            ->whereDate('zlecenie_data', '<', today())
            ->where(function ($query) {
                $query->where('ilosc_do_zwrotu', '>', 0);
                $query->orWhereNull('sprawdzone_at');
            })
            ->get();

        $naszykowane = $naszykowane->filter(function ($naszykowana) {
            return $naszykowana->towar and (! $naszykowana->zlecenie->is_odplatne or $naszykowana->ilosc_do_zwrotu > 0);
        });

        return $naszykowane->count();
    }
}
