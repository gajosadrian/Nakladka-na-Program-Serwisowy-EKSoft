<?php

namespace App\Config;

class Author
{
    public $name;
    public $url;

    public function __construct()
    {
        $this->name = 'Adrian Gajos';
        $this->url = '//agdev.pl';
    }
}
