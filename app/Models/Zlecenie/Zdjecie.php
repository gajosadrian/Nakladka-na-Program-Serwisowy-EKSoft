<?php

namespace App\Models\Zlecenie;

use Illuminate\Database\Eloquent\Model;

class Zdjecie extends Model
{
    protected $connection = 'mysql';
    protected $table = 'zlecenie_zdjecia';
    protected $guarded = [];
    protected $appends = ['url'];

    public const
        TYPE_GWARANCJA = 'gwarancja',   TYPE_POLISA = 'polisa',
        TYPE_DOWOD_ZAKUPU = 'dowod_zakupu', TYPE_URZADZENIE = 'urzadzenie',
        TYPE_TABLICZKA = 'tabliczka', TYPE_INNE = 'inne';

    public const TECHNIK_PATH = '# %s/%s/%s/%s';

    public function getIsDeletableAttribute(): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    public function getUrlAttribute(): string
    {
        return route('zlecenie-zdjecie.make', $this->id);
    }

    public function scopeOnlyType(string $type)
    {
        return $this->where('type', $type);
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

    public function scopeOnlyInne()
    {
        return $this->where('type', self::TYPE_INNE);
    }
}
