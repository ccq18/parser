<?php

use SyntaxAnalyzer\Analyzer;

require_once __DIR__ . '/testhelpers.php';


//create(field aaaa,field bbb,)
$words = [
    ['type' => 'key', 'value' => 'CREATE'],
    ['type' => 'symbol', 'value' => '('],
    ['type' => 'key', 'value' => 'FIELD'],
    ['type' => 'string', 'value' => 'aaaa'],
    ['type' => 'symbol', 'value' => ','],
    ['type' => 'key', 'value' => 'FIELD'],
    ['type' => 'string', 'value' => 'ccc'],
    ['type' => 'symbol', 'value' => ','],
    ['type' => 'key', 'value' => 'FIELD'],
    ['type' => 'string', 'value' => 'bbb'],
//    ['type' => 'symbol', 'value' => ','],
    ['type' => 'symbol', 'value' => ')'],

];

$rules = [
    'create' => [
        'matches' => [
            ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
            ['r' => '/\(/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
            ['r' => 'create_content', 'type' => 'call', 'n' => [1, PHP_INT_MAX], 'name' => 'fields'],
            ['r' => '/\)/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
        ],
        'n' => [1, PHP_INT_MAX],
        'name'=>'create',

    ],
    'create_content' => [
        'matches' => [
            ['r' => '/FIELD/', 'type' => 'key', 'n' => [1, 1], 'name' => 'field'],
            ['r' => '/.*/', 'type' => 'string', 'n' => [1, 1], 'name' => 'field'],
            ['r' => '/\,/', 'type' => 'symbol', 'n' => [0, 1], 'name' => 'symbol'],
        ],
        'n' => [1, PHP_INT_MAX],
        'name'=>'create_content',
    ]
];
$g = new Analyzer($rules);
$g->setWords($words);

$i = 0;
$r = $g->matchOne($rules['create'], $rs, $i);
print_r($rs);
//$g->showLog();

//