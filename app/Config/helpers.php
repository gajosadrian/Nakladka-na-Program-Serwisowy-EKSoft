<?php

/**
 * Handy Classes
 */
function program() { return new App\Config\Program; }
function author() { return new App\Config\Author; }

function getMonths(): array
{
    return [
        (object) [
            'id' => 1,
            'name' => 'Styczeń',
        ],
        (object) [
            'id' => 2,
            'name' => 'Luty',
        ],
        (object) [
            'id' => 3,
            'name' => 'Marzec',
        ],
        (object) [
            'id' => 4,
            'name' => 'Kwiecień',
        ],
        (object) [
            'id' => 5,
            'name' => 'Maj',
        ],
        (object) [
            'id' => 6,
            'name' => 'Czerwiec',
        ],
        (object) [
            'id' => 7,
            'name' => 'Lipiec',
        ],
        (object) [
            'id' => 8,
            'name' => 'Sierpień',
        ],
        (object) [
            'id' => 9,
            'name' => 'Wrzesień',
        ],
        (object) [
            'id' => 10,
            'name' => 'Październik',
        ],
        (object) [
            'id' => 11,
            'name' => 'Listopad',
        ],
        (object) [
            'id' => 12,
            'name' => 'Grudzień',
        ],
    ];
}

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
