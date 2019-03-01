<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;

class Pracownik extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'PRACOWNICY';
    protected $primaryKey = 'ID_PRACOWNIKA';

    /**
    * Attributes
    *
    */

	public function getIdAttribute(): string
    {
        return $this->attributes['ID_PRACOWNIKA'];
    }

	public function getLoginAttribute(): string
    {
        return $this->attributes['LOGIN'];
    }
}
