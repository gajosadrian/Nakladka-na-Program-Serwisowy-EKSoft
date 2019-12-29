<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class TowarStan extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tw_Stan';
    protected $primaryKey = 'st_TowId';

    public $timestamps = false;

    /**
     * Attributes
     *
     */

    public function getStanAttribute(): float
    {
        return $this->attributes['st_Stan'];
    }
}
