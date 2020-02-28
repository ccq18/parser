<?php

use SyntaxAnalyzer\Analyzer;

require_once __DIR__ . '/testhelpers.php';


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
    ],
    'n' => [1, 999],

];
$g = new Analyzer(null);
$g->setWords($words);

$i = 0;
$r = $g->matchNum($rule, $rs, $i, $rule['n'][0], $rule['n'][1]);
var_dump('rs', $i, $r, $rs);
//$g->showLog();

//