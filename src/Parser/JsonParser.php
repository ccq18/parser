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
                ['r' => '/[\s]/', 'n' => [1, PHP_INT_MAX]],
            ], 'type' => 'white'],
            //浮点数
            ['matches' => [
                ['r' => '/[0-9]/i', 'n' => [1, PHP_INT_MAX]],
                ['r' => '/\./', 'n' => [1, 1]],
                ['r' => '/[0-9]/i', 'n' => [1, PHP_INT_MAX]]

            ], 'type' => 'number'],
            //整数
            ['matches' => [
                ['r' => '/[0-9]/i', 'n' => [1, PHP_INT_MAX]],
            ], 'type' => 'int'],
            //字符串
            ['matches' => [
                ['r' => '/"/', 'n' => [1, 1]],
                ['r' => '/[^"]/i', 'n' => [0, PHP_INT_MAX]],
                ['r' => '/"/', 'n' => [1, 1]]
            ], 'type' => 'string'],
            //字符串2
            ['matches' => [
                ['r' => '/\'/', 'n' => [1, 1]],
                ['r' => '/[^\']/i', 'n' => [1, PHP_INT_MAX]],
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

    }
}