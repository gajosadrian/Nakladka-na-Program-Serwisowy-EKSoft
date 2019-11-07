<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class Zdjecie extends Model
{
    protected $connection = 'mysql';
    protected $table = 'zlecenie_zdjecia';
    protected $guarded = [];

    public const
        TYPE_GWARANCJA = 'gwarancja',   TYPE_POLISA = 'polisa',
        TYPE_DOWOD_ZAKUPU = 'd_zakupu', TYPE_URZADZENIE = 'urzadzenie',
        TYPE_TABLICZKA = 'tabliczka';

    public function getUrlAttribute(): string
    {
        return route('zlecenie-zdjecie.make', $this->id);
    }

    public function scopeOnlyGwarancja()
    {
        return $this->where('type', self::TYPE_GWARANCJA);
    }

    public function scopeOnlyPolisa()
    {
        return $this->where('type', self::TYPE_POLISA);
    }

    public function scopeOnlyDowodZakupu()
    {
        return $this->where('type', self::TYPE_DOWOD_ZAKUPU);
    }

    public function scopeOnlyUrzadzenie()
    {
        return $this->where('type', self::TYPE_URZADZENIE);
    }

    public function scopeOnlyTabliczka()
    {
        return $this->where('type', self::TYPE_TABLICZKA);
    }
}
