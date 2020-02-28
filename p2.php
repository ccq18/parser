<?php

use SyntaxAnalyzer\Analyzer;

require_once __DIR__ . '/testhelpers.php';


function test1()
{
    testRule('/[a-zA-Z0-9]+/i', ['a', 'a22'], [',', ';']);
    testRule('/[;,\(\)]+/i', [',', ';'], ['a', 'a22']);
    testRule('/[^"]+/i', ['a1', "sazda2'"], ['"']);

    testRule('/\s+/', [' \n'], ['"']);
}

$words = [
    ['type' => 'white', 'value' => ''],
    ['type' => 'key', 'value' => 'CREATE'],
    ['type' => 'white', 'value' => ''],
    ['type' => 'key', 'value' => 'CREATE']
];

$rule = [
    'matches' => [
        ['type' => 'white', 'n' => [0, 999], 'name' => 'white1'],
        ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
    ]];
$g = new Analyzer(null);
$g->setWords($words);
$i = 0;
$r = $g->matchOne($rule, $rs, $i);
var_dump('rs',$i, $r,$rs);
$r = $g->matchOne($rule, $rs, $i);
var_dump('rs',$i, $r,$rs);
//