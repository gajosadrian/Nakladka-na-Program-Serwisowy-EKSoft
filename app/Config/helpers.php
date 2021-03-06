<?php

/**
 * Handy Classes
 */
function program() { return new App\Config\Program; }
function author() { return new App\Config\Author; }

function getMonths()
{
    return collect([
        (object) [
            'id' => 1,
            'name' => 'Styczeń',
            'short_name' => 'STY',
        ],
        (object) [
            'id' => 2,
            'name' => 'Luty',
            'short_name' => 'LUT',
        ],
        (object) [
            'id' => 3,
            'name' => 'Marzec',
            'short_name' => 'MAR',
        ],
        (object) [
            'id' => 4,
            'name' => 'Kwiecień',
            'short_name' => 'KWI',
        ],
        (object) [
            'id' => 5,
            'name' => 'Maj',
            'short_name' => 'MAJ',
        ],
        (object) [
            'id' => 6,
            'name' => 'Czerwiec',
            'short_name' => 'CZE',
        ],
        (object) [
            'id' => 7,
            'name' => 'Lipiec',
            'short_name' => 'LIP',
        ],
        (object) [
            'id' => 8,
            'name' => 'Sierpień',
            'short_name' => 'SIE',
        ],
        (object) [
            'id' => 9,
            'name' => 'Wrzesień',
            'short_name' => 'WRZ',
        ],
        (object) [
            'id' => 10,
            'name' => 'Październik',
            'short_name' => 'PAŹ',
        ],
        (object) [
            'id' => 11,
            'name' => 'Listopad',
            'short_name' => 'LIS',
        ],
        (object) [
            'id' => 12,
            'name' => 'Grudzień',
            'short_name' => 'GRU',
        ],
    ]);
}

function array_sum_identical_keys(array $a1, array $a2): array
{
    $sums = [];
    foreach (array_keys($a1 + $a2) as $key) {
        $sums[$key] = round((isset($a1[$key]) ? $a1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0), 4);
    }
    return $sums;
}

function array_sub_identical_keys(array $a1, array $a2): array
{
    $sums = [];
    foreach (array_keys($a1 + $a2) as $key) {
        $sums[$key] = round((isset($a1[$key]) ? $a1[$key] : 0) - (isset($a2[$key]) ? $a2[$key] : 0), 4);
    }
    return $sums;
}

function get_string_between(string $string, string $start, string $end): string
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function str_contains2(string $str, $values): ?bool {
    switch (gettype($values)) {
        case 'string':
            return str_contains($str, $values);
            break;
        case 'array':
            foreach ($values as $value) {
                if (str_contains($str, $value)) {
                    return true;
                }
            }
            return false;
            break;
        default:
            return null;
            break;
    }
}

function string_matches_zipcode(?string $str): ?string {
    if (! $str) return null;
    if (preg_match("/[0-9]{2}-[0-9]{3}/", $str, $matches)) {
        if ($zipcode = $matches[0]) {
            return $zipcode;
        }
    }
    return null;
}
