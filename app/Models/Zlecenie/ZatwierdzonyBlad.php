<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class ZatwierdzonyBlad extends Model
{
    protected $connection = 'mysql';
    protected $table = 'zatwierdzone_bledy';
}
