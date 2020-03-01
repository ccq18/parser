<?php

use SyntaxAnalyzer\Analyzer;
use  Parser\AnalyzerRules;

require_once __DIR__ . '/testhelpers.php';

$p = new \LexicalAnalyzer\Parser(\Parser\JsonParser::getLexRule());

$s = '{"a":{"a":"b","c":"333"},"c":"b"}';
$s = '["a","q"]';
$s = '{"a":"bb","c":"b"}';
$s = '{"a":{"a":"b","c":"333"},"c":["a","q"],"d":"222"}';


$words = $p->run($s);
//print_r($words);
$rules = [
    'array' => AnalyzerRules::one()
        ->r('/\[/', 'symbol')
        ->r(['array_item'], 'call', 'items', 1, PHP_INT_MAX)
        ->r('/\]/', 'symbol')
        ->n(1, PHP_INT_MAX)
        ->end(function ($v) {
            $rs = [];
            foreach ($v['items'] as $v) {
                $rs[] = $v['value'];
            }
            return $rs;
        })
        ->get(),
    'array_item' => AnalyzerRules::one()
        ->r(['string', 'int'], 'call', 'value')
        ->r('/\,/', 'symbol', null, 0)
        ->end(function ($v) {
            return ['value' => $v['value'][0]];
        })
        ->get(),
    'object' => AnalyzerRules::one()
        ->r('/\{/', 'symbol')
        ->r(['field'], 'call', 'fields', 1, PHP_INT_MAX)
        ->r('/\}/', 'symbol')
        ->n(1, PHP_INT_MAX)
        ->end(function ($v) {
            $rs = [];
            foreach ($v['fields'] as $v) {
                $rs[$v['key']] = $v['value'];
            }
            return $rs;
        })
        ->get(),

    'field' => AnalyzerRules::one()
        ->r('/.*/', 'string', 'key')
        ->r('/\:/', 'symbol')
        ->r(['string', 'array', 'int', 'object'], 'call', 'value')
        ->r('/\,/', 'symbol', null, 0)
        ->end(function ($v) {
            return ['key' => $v['key'][0]['value'], 'value' => $v['value'][0]];
        })
        ->get(),
    'string' => AnalyzerRules::one()
        ->r(null, 'string', 'value')
        ->end(function ($v) {
            return $v['value'][0]['value'];
        })
        ->get(),
    'int' => AnalyzerRules::one()
        ->r(null, 'int', 'value')
        ->end(function ($v) {
            return $v['value'][0]['value'];
        })
        ->get(),
];
$g = new Analyzer($rules);

$i = 0;
$rs = $g->run($words);
print_r($rs[0]['value']);
print_r(json_encode($rs[0]['value']));
//$g->showLog();

//