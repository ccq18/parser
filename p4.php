<?php

use SyntaxAnalyzer\Analyzer;

require_once __DIR__ . '/testhelpers.php';


//create create
$words = [
    ['type' => 'white', 'value' => ''],
    ['type' => 'key', 'value' => 'CREATE'],
    ['type' => 'white', 'value' => ''],
    ['type' => 'key', 'value' => 'CREATE']
];

$rules = [
    'create' => [
        'matches' => [
            ['r' => 'create_content', 'type' => 'call', 'n' => [1, PHP_INT_MAX], 'name' => 'key_create'],
        ],
        'n' => [1, PHP_INT_MAX],

    ],
    'create_content' => [
        'matches' => [
            ['type' => 'white', 'n' => [0, PHP_INT_MAX], 'name' => 'white1'],
            ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
        ],
        'n' => [1, PHP_INT_MAX],
    ]
];
$g = new Analyzer( $rules);
$g->setWords($words);

$i = 0;
$r = $g->matchOne($rules['create'], $rs, $i);
var_dump('rs', $i, $r, $rs);
//$g->showLog();

//