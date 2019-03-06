<?php

namespace App\Models\Rozliczenie;

use Illuminate\Database\Eloquent\Model;

class RozliczoneZlecenie extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rozliczenie_rozliczone_zlecenia';
}
