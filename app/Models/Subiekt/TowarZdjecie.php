<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class TowarZdjecie extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tw_ZdjecieTw';
    protected $primaryKey = 'zd_Id';

    /**
    * Attributes
    *
    */

    public function getIdAttribute(): int
    {
        return $this->attributes['zd_Id'];
    }

    public function getTowarIdAttribute(): int
    {
        return $this->attributes['zd_IdTowar'];
    }

    public function getIsGlowneAttribute(): bool
    {
        return $this->attributes['zd_Glowne'];
    }

    public function getZdjecieBinaryAttribute()
    {
        return $this->attributes['zd_Zdjecie'];
    }
}
