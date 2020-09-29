<?php

namespace App\Config;

class Program
{
    public $name;
    public $description;
    public $verion;
    public $lastUpdateDate;

    public function __construct()
    {
        $this->name = 'Nakładka na EKSoft GT';
        $this->description = '';
        $this->version = '1.0';
        $this->lastUpdateDate = '2019-02-15';
    }
}
