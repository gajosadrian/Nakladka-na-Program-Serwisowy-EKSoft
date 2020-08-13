<?php

namespace App\Models\Subiekt;

use Illuminate\Database\Eloquent\Model;

class TowarCena extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tw_Cena';
    protected $primaryKey = 'tc_Id';

    public $timestamps = false;

    protected $visible = [
        'id', 'product_id', 'purchase_netto', 'purchase_brutto', 'netto1', 'netto2', 'netto3', 'netto4', 'netto5', 'netto6', 'netto7', 'netto8', 'netto9', 'netto10', 'brutto1', 'brutto2', 'brutto3', 'brutto4', 'brutto5', 'brutto6', 'brutto7', 'brutto8', 'brutto9', 'brutto10'
    ];
    protected $appends = [
        'id', 'product_id', 'purchase_netto', 'purchase_brutto', 'netto1', 'netto2', 'netto3', 'netto4', 'netto5', 'netto6', 'netto7', 'netto8', 'netto9', 'netto10', 'brutto1', 'brutto2', 'brutto3', 'brutto4', 'brutto5', 'brutto6', 'brutto7', 'brutto8', 'brutto9', 'brutto10'
    ];

    /**
     * Attributes
     *
     */
    public function getIdAttribute(): int
    {
        return $this->attributes['tc_Id'];
    }

    public function getProductIdAttribute(): int
    {
        return $this->attributes['tc_IdTowar'];
    }

    public function getPurchaseNettoAttribute(): float
    {
        return $this->attributes['tc_CenaNetto0'];
    }

    public function getPurchaseBruttoAttribute(): float
    {
        return $this->attributes['tc_CenaBrutto0'];
    }

    public function getNetto1Attribute(): float
    {
        return $this->getNetto(1);
    }

    public function getNetto2Attribute(): float
    {
        return $this->getNetto(2);
    }

    public function getNetto3Attribute(): float
    {
        return $this->getNetto(3);
    }

    public function getNetto4Attribute(): float
    {
        return $this->getNetto(4);
    }

    public function getNetto5Attribute(): float
    {
        return $this->getNetto(5);
    }

    public function getNetto6Attribute(): float
    {
        return $this->getNetto(6);
    }

    public function getNetto7Attribute(): float
    {
        return $this->getNetto(7);
    }

    public function getNetto8Attribute(): float
    {
        return $this->getNetto(8);
    }

    public function getNetto9Attribute(): float
    {
        return $this->getNetto(9);
    }

    public function getNetto10Attribute(): float
    {
        return $this->getNetto(10);
    }

    public function getBrutto1Attribute(): float
    {
        return $this->getBrutto(1);
    }

    public function getBrutto2Attribute(): float
    {
        return $this->getBrutto(2);
    }

    public function getBrutto3Attribute(): float
    {
        return $this->getBrutto(3);
    }

    public function getBrutto4Attribute(): float
    {
        return $this->getBrutto(4);
    }

    public function getBrutto5Attribute(): float
    {
        return $this->getBrutto(5);
    }

    public function getBrutto6Attribute(): float
    {
        return $this->getBrutto(6);
    }

    public function getBrutto7Attribute(): float
    {
        return $this->getBrutto(7);
    }

    public function getBrutto8Attribute(): float
    {
        return $this->getBrutto(8);
    }

    public function getBrutto9Attribute(): float
    {
        return $this->getBrutto(9);
    }

    public function getBrutto10Attribute(): float
    {
        return $this->getBrutto(10);
    }

    /**
     * Methods
     *
     */
    public function getNetto(int $key): float
    {
        return $this->attributes['tc_CenaNetto'.$key];
    }

    public function getBrutto(int $key): float
    {
        return $this->attributes['tc_CenaBrutto'.$key];
    }

    public function setPurcharseNetto(float $value)
    {
        $this->attributes['tc_CenaNetto0'] = $value;
        $this->attributes['tc_CenaNettoWaluta'] = $value;
        $this->attributes['tc_CenaNettoWaluta2'] = $value;
        $this->attributes['tc_CenaWalutaNarzut'] = $value;
    }

    public function setNetto(int $key, float $value_netto, float $vat, float $currency_rate = 1): void
    {
        if ($key < 1 or $key > 10) return;

        $value_netto = $value_netto * $currency_rate;
        $value_brutto = $value_netto * (1 + $vat);
        $purchase_netto = $this->purchase_netto * $currency_rate;

        $this->attributes['tc_CenaNetto'.$key] = $value_netto;
        $this->attributes['tc_CenaBrutto'.$key] = $value_brutto;
        $this->attributes['tc_Zysk'.$key] = $value_netto - $purchase_netto;
        $this->attributes['tc_Narzut'.$key] = (($value_netto / $purchase_netto) - 1) * 100;
        $this->attributes['tc_Marza'.$key] = (1 - ($purchase_netto / $value_netto)) * 100;
    }
}
