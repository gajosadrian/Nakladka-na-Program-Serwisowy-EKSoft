<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StatusHistoria extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ser_ZlecStatHist';
    protected $primaryKey = 'id_hist';
    protected $with = ['status', 'pracownik'];
    public $timestamps = false;

    /**
    * Attributes
    *
    */

    public function getIdAttribute(): string
    {
        return $this->attributes['id_hist'];
    }

    public function getStatusIdAttribute(): string
    {
        return $this->status->id;
    }

    public function getNazwaAttribute(): string
    {
        return $this->status->nazwa;
    }

    public function getDataAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['data']);
    }

    public function getDataFormattedAttribute(): String
    {
        return $this->data->format('Y-m-d H:i');
    }

    public function getGodzinaFormattedAttribute(): String
    {
        return $this->data->format('H:i');
    }

    public function setPracownikIdAttribute(int $value): void
    {
        $this->attributes['id_user'] = $value;
    }

    public function setStatusIdAttribute(int $value): void
    {
        $this->attributes['id_status'] = $value;
    }

    public function setZlecenieIdAttribute(int $value): void
    {
        $this->attributes['id_zs'] = $value;
    }

    /**
    * Relations
    *
    */

    public function status()
    {
        return $this->hasOne('App\Models\Zlecenie\Status', 'id_stat', 'id_status')->withDefault([
            'status' => 'Brak statusu',
        ]);
    }

    public function pracownik()
    {
        return $this->hasOne('App\Models\SMS\Pracownik', 'ID_PRACOWNIKA', 'id_user');
    }
}
