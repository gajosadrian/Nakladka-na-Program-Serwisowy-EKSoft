<?php

namespace App\Models\Inwentaryzacja;

use Illuminate\Database\Eloquent\Model;

class Stan extends Model
{
    protected $connection = 'mysql';
    protected $table = 'inwentaryzacja_stany';
    protected $guarded = [];
    // protected $dates = ['closed_at'];
    // protected $casts = [
    //     '' => 'array',
    // ];

    /**
     * Attributes
     *
     */
}