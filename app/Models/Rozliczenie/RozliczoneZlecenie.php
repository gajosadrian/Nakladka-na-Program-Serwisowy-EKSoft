<?php

namespace App\Models\Rozliczenie;

use App\Models\Zlecenie\Zlecenie;
use Illuminate\Database\Eloquent\Model;

class RozliczoneZlecenie extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rozliczenie_rozliczone_zlecenia';
    protected $guarded = [];
    protected $casts = [
        'robocizny' => 'array',
        'dojazdy' => 'array',
    ];

    /**
     * Attributes
     *
     */

    public function getRobociznyHtmlAttribute(): string
    {
        return Zlecenie::getHtmlKosztorys('ROBOCIZNY', $this->robocizny, 'info');
    }

    public function getDojazdyHtmlAttribute(): string
    {
        return Zlecenie::getHtmlKosztorys('DOJAZDY', $this->dojazdy, 'success');
    }

    public function getSumaRobociznAttribute(): float
    {
        return array_sum($this->robocizny);
    }

    public function getSumaDojazdowAttribute(): float
    {
        return array_sum($this->dojazdy);
    }

    /**
     * Relations
     *
     */

    public function zlecenie()
    {
        return $this->hasOne('App\Models\Zlecenie\Zlecenie', 'id_zlecenia', 'zlecenie_id');
    }

    public function rozliczenie()
    {
        return $this->belongsTo('App\Models\Rozliczenie\Rozliczenie');
    }
}
