<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class TelefonEwidencja extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tel__Ewid';
    protected $primaryKey = 'tel_Id';
    protected $cast = [
        'tel_Podstawowy' => 'boolean',
    ];

    public const NR_KIERUNKOWE = [12,13,14,15,16,17,18,22,23,24,25,29,32,33,34,41,42,43,44,46,48,52,54,55,56,58,59,61,62,63,65,67,68,71,74,75,76,77,81,82,83,84,85,86,87,89,91,94,95];

    /**
    * Attributes
    *
    */

    public function getNrAttribute(): string
    {
        return $this->attributes['tel_Numer'];
    }

    public function getPhoneNrAttribute(): string
    {
        preg_match_all('!\d+!', $this->nr, $phone);
        return implode('', $phone[0]);
    }

    public function getIsPodstawowyAttribute(): bool
    {
        return $this->attributes['tel_Podstawowy'];
    }

    public function getIsStacjonarnyAttribute(): bool
    {
        if (strlen($this->phone_nr) != 9) return false;
        $phone_kierunkowy = substr($this->phone_nr, 0, 2);
        return in_array($phone_kierunkowy, self::NR_KIERUNKOWE);
    }

    public function getIsKomorkowyAttribute(): bool
    {
        if (strlen($this->phone_nr) != 9) return false;
        return ! $this->is_stacjonarny;
    }
}
