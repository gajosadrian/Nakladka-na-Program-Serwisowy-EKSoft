<?php

/**
 * Handy Classes
 */
function program() { return new App\Config\Program; }
function author() { return new App\Config\Author; }

function array_sum_identical_keys(array $a1, array $a2): array
{
    $sums = [];
    foreach (array_keys($a1 + $a2) as $key) {
        $sums[$key] = (isset($a1[$key]) ? $a1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0);
    }
    return $sums;
}

function array_sub_identical_keys(array $a1, array $a2): array
{
    $sums = [];
    foreach (array_keys($a1 + $a2) as $key) {
        $sums[$key] = (isset($a1[$key]) ? $a1[$key] : 0) - (isset($a2[$key]) ? $a2[$key] : 0);
    }
    return $sums;
}
