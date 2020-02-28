<?php

use SyntaxAnalyzer\Analyzer;
use  Parser\AnalyzerRules;

require_once __DIR__ . '/testhelpers.php';

$p = new \Parser\JsonParser();

$ss = [
    '{"a":{"a":"b","c":"333"},"c":"b"}',
    '["a","q"]',
    '{"a":"bb","c":"b"}',
    '{"a":{"a":"b","c":"333"},"c":["a","q"],"d":"222"}'
];
foreach ($ss as $s) {
    $rs = $p->parser($s);
    print_r($rs);
}