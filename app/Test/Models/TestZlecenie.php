<?php

namespace App\Test\Models;

use Illuminate\Database\Eloquent\Model;

class TestZlecenie extends Model
{
    protected $connection = 'test';
    protected $table = 'adrian_test.dbo.test_zlecenia';
    protected $guarded = [];

    /**
     * Relations
     *
     */
    public function zlecenie()
    {
        return $this->belongsTo('App\Models\Zlecenie\Zlecenie', 'id_zlecenia', 'zlecenie_id');
    }
}
