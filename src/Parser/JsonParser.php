<?php

namespace Parser;


use SyntaxAnalyzer\Analyzer;
use SyntaxAnalyzer\AnalyzerRules;

class JsonParser
{
    protected $lexParser, $analyzer;

    function __construct()
    {
        $this->lexParser = new \LexicalAnalyzer\Parser($this->getLexRule());
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
                ['r' => '/[\s]/', 'n' => [1, 999]],
            ], 'type' => 'white'],
            //浮点数
            ['matches' => [
                ['r' => '/[0-9]/i', 'n' => [1, 999]],
                ['r' => '/\./', 'n' => [1, 1]],
                ['r' => '/[0-9]/i', 'n' => [1, 999]]

            ], 'type' => 'number'],
            //整数
            ['matches' => [
                ['r' => '/[0-9]/i', 'n' => [1, 999]],
            ], 'type' => 'int'],
            //字符串
            ['matches' => [
                ['r' => '/"/', 'n' => [1, 1]],
                ['r' => '/[^"]/i', 'n' => [1, 999]],
                ['r' => '/"/', 'n' => [1, 1]]
            ], 'type' => 'string'],
            //字符串2
            ['matches' => [
                ['r' => '/\'/', 'n' => [1, 1]],
                ['r' => '/[^\']/i', 'n' => [1, 999]],
                ['r' => '/\'/', 'n' => [1, 1]]
            ], 'type' => 'string'],
        ];
        return array_merge($r0, $rules);

    }

    static function getAnalyzerRules()
    {
        return [
            'array' => AnalyzerRules::one()
                ->r('symbol', '/\[/')
                ->r('call', ['array_item'], 'items', 1, 999)
                ->r('symbol', '/\]/')
                ->n(1, 999)
                ->end(function ($v) {
                    $rs = [];
                    foreach ($v['items'] as $v) {
                        $rs[] = $v['value'];
                    }
                    return $rs;
                })
                ->get(),
            'array_item' => AnalyzerRules::one()
                ->r('call', ['string', 'int'], 'value')
                ->r('symbol', '/\,/', null, 0)
                ->end(function ($v) {
                    return ['value' => $v['value'][0]];
                })
                ->get(),
            'object' => AnalyzerRules::one()
                ->r('symbol', '/\{/')
                ->r('call', ['field'], 'fields', 1, 999)
                ->r('symbol', '/\}/')
                ->n(1, 999)
                ->end(function ($v) {
                    $rs = [];
                    foreach ($v['fields'] as $v) {
                        $rs[$v['key']] = $v['value'];
                    }
                    return $rs;
                })
                ->get(),

            'field' => AnalyzerRules::one()
                ->r('string', '/.*/', 'key')
                ->r('symbol', '/\:/')
                ->r('call', ['string', 'array', 'int', 'object'], 'value')
                ->r('symbol', '/\,/', null, 0)
                ->end(function ($v) {
                    return ['key' => $v['key'][0]['value'], 'value' => $v['value'][0]];
                })
                ->get(),
            'string' => AnalyzerRules::one()
                ->r('string', null, 'value')
                ->end(function ($v) {
                    return $v['value'][0]['value'];
                })
                ->get(),
            'int' => AnalyzerRules::one()
                ->r('int', null, 'value')
                ->end(function ($v) {
                    return $v['value'][0]['value'];
                })
                ->get(),
        ];

    }
}