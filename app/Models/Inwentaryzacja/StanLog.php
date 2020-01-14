<?php

namespace App\Models\Inwentaryzacja;

use Illuminate\Database\Eloquent\Model;

class StanLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'inwentaryzacja_stan_logs';
    protected $guarded = [];

    /**
     * Relations
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
