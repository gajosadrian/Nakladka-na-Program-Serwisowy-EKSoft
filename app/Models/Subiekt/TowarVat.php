<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class TowarVat extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'sl_StawkaVAT';
    protected $primaryKey = 'vat_Id';

    public $timestamps = false;

    /**
     * Attributes
     *
     */
    public function getIdAttribute(): int
    {
        return $this->attributes['vat_Id'];
    }

    public function getSymbolAttribute(): string
    {
        return $this->attributes['vat_Symbol'];
    }

    public function getValuePercentAttribute(): float
    {
        return $this->attributes['vat_Stawka'];
    }

    public function getValueAttribute(): float
    {
        return $this->value_percent / 100;
    }
}
