<?php

namespace App\Models\Inwentaryzacja;

use Illuminate\Database\Eloquent\Model;

class Stan extends Model
{
    protected $connection = 'mysql';
    protected $table = 'inwentaryzacja_stany';
    protected $guarded = [];

    /**
     * Relations
     *
     */
    public function towar()
    {
        return $this->belongsTo('App\Models\Subiekt\Subiekt_Towar', 'towar_id', 'tw_Id');
    }
}
