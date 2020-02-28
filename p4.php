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
            ['r' => 'create_content', 'type' => 'call', 'n' => [1, 999], 'name' => 'key_create'],
        ],
        'n' => [1, 999],

    ],
    'create_content' => [
        'matches' => [
            ['type' => 'white', 'n' => [0, 999], 'name' => 'white1'],
            ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
        ],
        'n' => [1, 999],
    ]
];
$g = new Analyzer( $rules);
$g->setWords($words);

$i = 0;
$r = $g->matchOne($rules['create'], $rs, $i);
var_dump('rs', $i, $r, $rs);
//$g->showLog();

//