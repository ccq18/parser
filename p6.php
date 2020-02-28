<?php

use SyntaxAnalyzer\Analyzer;

require_once __DIR__ . '/testhelpers.php';

$p = new \LexicalAnalyzer\Parser(\LexicalAnalyzer\LexRules::getSqlRules());
$words = [
    ['type' => 'key', 'value' => 'CREATE'],
    ['type' => 'symbol', 'value' => '('],
    ['type' => 'key', 'value' => 'FIELDS'],
    ['type' => 'white', 'value' => ''],
    ['type' => 'string', 'value' => 'aaaa'],
    ['type' => 'symbol', 'value' => ','],
    ['type' => 'key', 'value' => 'FIELDS'],
    ['type' => 'white', 'value' => ''],
    ['type' => 'string', 'value' => 'ccc'],
    ['type' => 'symbol', 'value' => ','],
    ['type' => 'key', 'value' => 'FIELDS'],
    ['type' => 'white', 'value' => ''],
    ['type' => 'string', 'value' => 'bbb'],
    ['type' => 'symbol', 'value' => ')'],

];
$s = 'CREATE(FIELDS "aaaa",FIELDS "ccc",FIELDS "bbb",);';
$words = $p->run($s);
//print_r($words);exit;
$rules = [
    'create' => [
        'matches' => [
            ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
            ['r' => '/\(/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
            ['r' => 'create_content', 'type' => 'call', 'n' => [1, 999], 'name' => 'fields'],
            ['r' => '/\)/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
            ['r' => '/\;/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
        ],
        'n' => [1, 999],
        'name' => 'create',

    ],
    'create_content' => [
        'matches' => [
            ['r' => '/FIELDS/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_field'],
            ['type' => 'white', 'n' => [0, 1], 'name' => 'white'],
            ['r' => '/.*/', 'type' => 'string', 'n' => [1, 1], 'name' => 'field_name'],
            ['r' => '/\,/', 'type' => 'symbol', 'n' => [0, 1], 'name' => 'symbol'],
        ],
        'n' => [1, 999],
        'name' => 'create_content',
    ]
];
$g = new Analyzer($rules);
$g->setWords($words);

$i = 0;
$r = $g->matchOne($rules['create'], $rs, $i);
print_r($rs);
//$g->showLog();

//