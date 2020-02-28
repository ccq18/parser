<?php

use SyntaxAnalyzer\Analyzer;
use  SyntaxAnalyzer\AnalyzerRules;

require_once __DIR__ . '/testhelpers.php';

$p = new \LexicalAnalyzer\Parser(\LexicalAnalyzer\LexRules::getSqlRules());

$s = 'CREATE(FIELDS "aaaa",FIELDS "ccc",FIELDS "bbb",);';
$words = $p->run($s);
$rules = [
    'create' => AnalyzerRules::one()
        ->r('key', '/CREATE/')
        ->r('symbol', '/\(/')
        ->r('call', 'create_content', 'fields', 1, 999)
        ->r('symbol', '/\)/')
        ->r('symbol', '/\;/')
        ->n(1, 999)
        ->get(),
    'create_content' => AnalyzerRules::one()
        ->r('key','/FIELDS/')
        ->r('white',null,null,0,1)
        ->r('string','/.*/','field_name')
        ->r('symbol', '/\,/')
        ->get(),
];
$g = new Analyzer($rules);
$g->setWords($words);

$i = 0;
$rs = $g->run($words);
print_r($rs);
//$g->showLog();

//