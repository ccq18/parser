<?php

use SyntaxAnalyzer\Analyzer;
use  Parser\AnalyzerRules;

require_once __DIR__ . '/testhelpers.php';

$p = new \LexicalAnalyzer\Parser(Parser\LexRules::getSqlRules());

$s = 'CREATE(FIELDS "aaaa",FIELDS "ccc",FIELDS "bbb",);';
$words = $p->run($s);
$rules = [
    'create' => AnalyzerRules::one()
        ->r('/CREATE/', 'key')
        ->r('/\(/', 'symbol')
        ->r('create_content', 'call', 'fields', 1, PHP_INT_MAX)
        ->r('/\)/', 'symbol')
        ->r('/\;/', 'symbol')
        ->n(1, PHP_INT_MAX)
        ->get(),
    'create_content' => AnalyzerRules::one()
        ->r('/FIELDS/', 'key')
        ->r(null, 'white', null, 0, 1)
        ->r('/.*/', 'string', 'field_name')
        ->r('/\,/', 'symbol')
        ->get(),
];
$g = new Analyzer($rules);
$g->setWords($words);

$i = 0;
$rs = $g->run($words);
print_r($rs);
//$g->showLog();

//