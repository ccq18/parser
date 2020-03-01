<?php

use SyntaxAnalyzer\Analyzer;
use  Parser\AnalyzerRules;

require_once __DIR__ . '/testhelpers.php';

$p = new \Parser\JsonParser();

$ss = [
    '{"a":{"a":"b","c":"333"},"c":"b"}',
    '["e","q"]',
    '{"f":"bb","c":"b"}',
    '{"g":{"a":"b","c":333},"c":["a","q"],"d":"222","e":""}'
];
foreach ($ss as $s) {
    $rs = $p->parser($s);
    var_dump("json:", $s, 'value:', $rs);
}
