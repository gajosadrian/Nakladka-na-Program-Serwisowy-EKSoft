<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zlecenie_Status extends Model
{
    protected $table = 'SYSTEM_STATUS';
    protected $primaryKey = 'id_stat';

    public static $id_zakonczonych = [26, 29];

    public function getIdAttribute($value)
    {
        return $this->id_stat;
    }

    public function getNazwaAttribute($value)
    {
        return $this->status;
    }
}
