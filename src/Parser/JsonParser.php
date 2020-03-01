<?php

namespace Parser;


use LexicalAnalyzer\Parser;
use SyntaxAnalyzer\Analyzer;
use Parser\AnalyzerRules;

class JsonParser
{
    protected $lexParser, $analyzer;

    function __construct()
    {
        $this->lexParser = new Parser($this->getLexRule());
        $this->analyzer = new Analyzer($this->getAnalyzerRules());

    }

    function parser($s)
    {
        $words = $this->lexParser->run($s);
        $rs = $this->analyzer->run($words);
        return $rs[0]['value'];
    }

    public static function getLexRule()
    {
        // {"":"":,}[]
        $r0 = \Parser\LexRules::generateRules([
            '[',
            ']',
            '{',
            '}',
            ',',
            ':',
        ], 'symbol');

        $rules = [
            //空白
            ['matches' => [
                ['r' => '/[\s]/', 'n' => [1]],
            ], 'type' => 'white'],
            //浮点数
            ['matches' => [
                ['r' => '/[0-9]/i', 'n' => [1]],
                ['r' => '/\./', 'n' => [1, 1]],
                ['r' => '/[0-9]/i', 'n' => [1]]

            ], 'type' => 'number'],
            //整数
            ['matches' => [
                ['r' => '/[0-9]/i', 'n' => [1]],
            ], 'type' => 'int'],
            //字符串
            ['matches' => [
                ['r' => '/"/', 'n' => [1, 1]],
                ['r' => '/[^"]/i', 'n' => [0]],
                ['r' => '/"/', 'n' => [1, 1]]
            ], 'type' => 'string'],
            //字符串2
            ['matches' => [
                ['r' => '/\'/', 'n' => [1, 1]],
                ['r' => '/[^\']/i', 'n' => [1]],
                ['r' => '/\'/', 'n' => [1, 1]]
            ], 'type' => 'string'],
        ];
        return array_merge($r0, $rules);

    }

    static function getAnalyzerRules()
    {
        return [
            'array' => AnalyzerRules::one()
                ->r('/\[/', 'symbol')
                ->r('array_item', 'call', 'items', 1, PHP_INT_MAX)
                ->r('/\]/', 'symbol')
                ->n(1, PHP_INT_MAX)
                ->after(function ($v) {
                    $rs = [];
                    foreach ($v['items'] as $vv) {
                        $rs[] = $vv['value'];
                    }
                    return $rs;
                })
                ->get(),
            'array_item' => AnalyzerRules::one()
                ->r(['string', 'int'], 'call', 'value')
                ->r('/\,/', 'symbol', null, 0)
                ->after(function ($v) {
                    return ['value' => $v['value'][0]['value']];
                })
                ->get(),
            'object' => AnalyzerRules::one()
                ->r('/\{/', 'symbol')
                ->r(['field'], 'call', 'fields', 1, PHP_INT_MAX)
                ->r('/\}/', 'symbol')
                ->n(1, PHP_INT_MAX)
                ->after(function ($v) {
                    $rs = [];
                    foreach ($v['fields'] as $vv) {
                        $rs[$vv['value']['key']] = $vv['value']['value'];
                    }
                    return $rs;
                })
                ->get(),

            'field' => AnalyzerRules::one()
                ->r('string', 'call', 'key')
                ->r('/\:/', 'symbol')
                ->r(['string', 'array', 'int', 'object'], 'call', 'value')
                ->r('/\,/', 'symbol', null, 0)
                ->after(function ($v) {
//                    print_r($v);exit;
                    return ['key' => $v['key'][0], 'value' => $v['value'][0]['value']];
                })
                ->get(),
            'string' => AnalyzerRules::one()
                ->r(null, 'string', 'value')
                ->after(function ($v) {
                    return $v['value'][0]['value'];
                })
                ->get(),
            'int' => AnalyzerRules::one()
                ->r(null, 'int', 'value')
                ->after(function ($v) {
                    return $v['value'][0]['value'];
                })
                ->get(),
        ];

    }
}