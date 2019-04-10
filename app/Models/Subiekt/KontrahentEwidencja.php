<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class KontrahentEwidencja extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'adr__Ewid';
    protected $primaryKey = 'adr_Id';
    protected $with = ['telefony_ewidencja'];

    /**
    * Attributes
    *
    */

    public function getTypAdresuAttribute()
    {
        return $this->attributes['adr_TypAdresu'];
    }

    public function getNazwaAttribute()
    {
        return $this->attributes['adr_Nazwa'];
    }

    public function getPelnaNazwaAttribute()
    {
        return $this->attributes['adr_NazwaPelna'];
    }

    public function getTelefonAttribute()
    {
        return $this->attributes['adr_Telefon'];
    }

    public function getAdresAttribute()
    {
        return $this->attributes['adr_Adres'];
    }

    public function getKodPocztowyAttribute()
    {
        return $this->attributes['adr_Kod'];
    }

    public function getMiejscowoscAttribute()
    {
        return $this->attributes['adr_Miejscowosc'];
    }

    public function getTelefonyAttribute(): object
    {
        return $this->telefony_ewidencja->sortByDesc('tel_Podstawowy');
    }

    /**
     * Relations
     *
     */

    public function telefony_ewidencja()
    {
        return $this->hasMany('App\Models\Subiekt\TelefonEwidencja', 'tel_IdAdresu', 'adr_Id');
    }
}
