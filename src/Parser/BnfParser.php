<?php

namespace Parser;

use LexicalAnalyzer\Parser;
use SyntaxAnalyzer\Analyzer;
use Parser\AnalyzerRules;


class BnfParser
{
    protected $lexParser, $analyzer;

    function __construct()
    {
        $this->lexParser = new Parser($this->getLexRule());
        $this->analyzer = new Analyzer($this->getAnalyzerRules());

    }

    /**
     * @param $s
     * @return [
     * ]
     */
    function parser($s)
    {
        $words = $this->lexParser->run($s);
        $rs = $this->analyzer->run($words);
        $rr = [];
        foreach ($rs as $v){
            $rr[]  = $v['value'][0];
        }
        return $rr;
    }

    public static function getLexRule()
    {
        // {"":"":,}[]
        $r0 = \Parser\LexRules::generateRules([
            '[',
            ']',
            '{',
            '}',
            '(',
            ')',
            '|',
            '::=',
            ';',
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
            ['matches' => [
                ['r' => '/\</', 'n' => [1, 1]],
                ['r' => '/[a-zA-Z0-9]/i', 'n' => [1, PHP_INT_MAX]],
                ['r' => '/\>/', 'n' => [1, 1]],
            ], 'type' => 'key'],
            //字符串
            ['matches' => [
                ['r' => '/"/', 'n' => [1, 1]],
                ['r' => '/[^"]/i', 'n' => [1, PHP_INT_MAX]],
                ['r' => '/"/', 'n' => [1, 1]]
            ], 'type' => 'string'],
            //字符串2
//            ['matches' => [
//                ['r' => '/\'/', 'n' => [1, 1]],
//                ['r' => '/[^\']/i', 'n' => [1, PHP_INT_MAX]],
//                ['r' => '/\'/', 'n' => [1, 1]]
//            ], 'type' => 'string'],
        ];
        return array_merge($r0, $rules);

    }

    //在双引号中的字 "word" 代表着这些字符本身。而double_quote用来代表双引号；
//在双引号外的字（有可能有下划线）代表着语法部分；
//尖括号 < > 内包含的为必选项；
//方括号 [ ] 内包含的为可选项；
//大括号 { } 内包含的为可重复0至无数次的项；
//圆括号 ( ) 内包含的所有项为一组，用来控制表达式的优先级；
//竖线 | 表示在其左右两边任选一项，相当于"OR"的意思；
//    ::= 是“被定义为”的意思；
//...  表示术语符号；
//斜体字: 参数，在其它地方有解释

//非终结符用尖括号括起。
////每条规则的左部是一个非终结符，右部是由非终结符和终结符组成的一个符号串，中间一般以::=分开。
////具有相同左部的规则可以共用一个左部，各右部之间以直竖“|”隔开


//<rule> ::= <identifier> "::=" <expression>
//<expression> ::= <factor> {term}
//<term> ::= "|" <factor>
//<factor> ::= <identifier> | <quoted_symbol> | <expression_c> | <expression_d> | <expression_e>
//<expression_c>::="(" <expression> ")"
//<expression_d>::="[" <expression> "]"
//<expression_e>::="{" <expression> "}"
//<identifier> ::= letter { letter | digit }
//<quoted_symbol> ::= ""
    static function getAnalyzerRules()
    {
        return [
            'rule' => AnalyzerRules::one()
                ->r('rule1', 'call', 'rule1')
                ->r('rule2', 'call', 'rule2', 0, PHP_INT_MAX)
                ->after(function ($v) {
                    $vv = [];
                    if(isset($v['rule1'])){
                        $vv = array_merge($vv,$v['rule1']);
                    }
                    if(isset($v['rule2'])){
                        $vv = array_merge($vv,$v['rule2']);
                    }

                    return $vv;
                })
                ->get(),
            'rule1' => AnalyzerRules::one()
                ->r('identifier', 'call', 'rule')
                ->r("::=")
                ->r('expression', 'call', 'expression')
                ->r(null, 'white', null, 0, 1)
                ->after(function ($v) {
                    $rs = [];
                    $rs['rule'] = $v['rule'][0];
                    $rs['expression'] = $v['expression'][0];
                    return $rs;
                })
                ->n(0, 0)
                ->get(),
            'rule2' => AnalyzerRules::one()
                ->r(null, 'white')
                ->r('identifier', 'call', 'rule')
                ->r("::=")
                ->r('expression', 'call', 'expression')
                ->r(null, 'white')
                ->after(function ($v) {
                    $rs = [];
                    $rs['rule'] = $v['rule'][0];
                    $rs['expression'] = $v['expression'][0];
                    return $rs;
                })
                ->n(0, 0)
                ->get(),
            'expression' => AnalyzerRules::one()
                ->r('factor', 'call', 'factor', 1, PHP_INT_MAX)
                ->r('term', 'call', 'term', 0, PHP_INT_MAX)
                ->after(function ($v) {
                    return array_merge($v['factor'],$v['term']);
                })
                ->n(0, 0)
                ->get(),
            'term' => AnalyzerRules::one()
                ->r("|")
                ->r('factor',null,'factor')
                ->after(function ($v) {
                    print_r($v);exit();
                    return $v;
                })
                ->n(0, 0)
                ->get(),
            'factor' => AnalyzerRules::one()
                ->r(['identifier', 'quoted_symbol', 'expression_c', 'expression_d', 'expression_e'], 'call', 'factor')
                ->after(function ($v) {
                    return $v['factor'][0];
                })
                ->n(0, 0)
                ->get(),
            'expression_c' => AnalyzerRules::one()
                ->r("(")
                ->r(['expression'], 'call', 'expression')
                ->r(")")
                ->after(function ($v) {
                    return $v['expression'][0]['value'];
                })
                ->n(0, 0)
                ->get(),
            'expression_d' => AnalyzerRules::one()
                ->r("[")
                ->r(['expression'], 'call', 'expression')
                ->r("]")
                ->after(function ($v) {
                    return $v['expression'][0]['value'];
                })
                ->n(0, 0)
                ->get(),
            'expression_e' => AnalyzerRules::one()
                ->r("{")
                ->r(['expression'], 'call', 'expression')
                ->r("}")
                ->after(function ($v) {
                    return $v['expression'][0]['value'];
                })
                ->n(0, 0)
                ->get(),
            'quoted_symbol' => AnalyzerRules::one()
                ->r(null, 'string', 'value')
                ->after(function ($v) {
                    return $v['value'][0]['value'];
                })
                ->n(0, 0)
                ->get(),
            'identifier' => AnalyzerRules::one()
                ->r(null, 'key', 'value')
                ->after(function ($v) {
                    return $v['value'][0]['value'];
                })
                ->n(0, 0)
                ->get(),

        ];

    }


}