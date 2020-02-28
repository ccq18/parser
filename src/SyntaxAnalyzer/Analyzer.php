<?php

namespace SyntaxAnalyzer;


class Analyzer
{
    protected $grammars = [];
    protected $words;
    protected $i;
    protected $rules;

    protected $log;

    function log($title, $data)
    {
        $this->log[] = $title . ',' . var_export($data, true);
    }

    function showLog()
    {
        print_r($this->log);
    }

    function __construct($rules)
    {
        $this->i = 0;
        $this->rules = $rules;
    }

    function setWords($words)
    {
        $this->words = $words;
    }

    /**
     * @param $words [['type'=> 'value'=>]]
     * @return array
     */
    function run($words)
    {
        $this->words = $words;
        while (!$this->isend($this->i)) {
            $m = true;
            foreach ($this->rules as $name => $rule) {
                $i = $this->i;
                //传入当前的i 若匹配成功 则i 应用
                $m = $this->matchOne($rule, $rs, $i);
                //匹配成功则保存
                if ($m) {
                    $this->i = $i;
                    $this->grammars[] = ['name' => $name, 'value' => $rs];
                    break;
                }
            }
            if (!$m) {
                out('匹配失败' . var_export($this->words[$this->i], true), 'error');
                return;
            };
        }
        return $this->grammars;

    }


    function matchNum($rule, &$rs, &$i, $min, $max)
    {
        $n = 0;
        while (!$this->isend($i)) {
            if ($n >= $max) {
                break;
            }
            if (is_array($rule)) {
                $m = false;
                foreach ($rule as $rr) {
                    $i0 = $i;
                    if ($this->matchOne($this->rules[$rr], $result, $i0)) {
                        $rs[] = $result;
                        $i = $i0;
                        $n++;
                        $m = true;
                        break;
                    }
                }
                if (!$m) {
                    break;
                }

            } else {

                $i0 = $i;
                if ($this->matchOne($this->rules[$rule], $result, $i0)) {
                    $rs[] = $result;
                    $i = $i0;
                    $n++;
                } else {
                    break;
                }

            }

        }
        return $n >= $min;

    }

    function matchOne($rule, &$rs, &$i)
    {
        $rs = [];
        //遍历所有规则
        foreach ($rule['matches'] as $r) {

            if ($r['type'] == 'call') {
                $i0 = $i;

                if ($this->matchNum($r['r'], $result, $i0, $r['n'][0], $r['n'][1])) {
                    $rs[$r['name']] = $result;
                    $i = $i0;
                } else {
                    return false;
                }
            } else {

                $n = 0;
                $w = null;
                while (!$this->isend($i)) {
                    //匹配结束 退出
                    //超过最大匹配次数则跳出当前
                    if ($n >= $r['n'][1]) {
                        break;
                    }
                    if (!$this->checkrule($r, $this->words[$i])) {
                        break;
                    }
                    $w = $this->words[$i]['value'];
                    $rs[$r['name']][] = $this->words[$i];
                    $i++;
                    $n++;
                }
                //匹配次数不满足
                if ($n < $r['n'][0]) {
                    $this->log('match', [$n, $r['n'][0], $w, $r['r'] ?? "", false]);
                    return false;
                } else {
                    $this->log('match', [$w, $r['r'] ?? "", true]);
                }
            }
        }
        if (!empty($rule['after'])) {
            $rs = $rule['after']($rs);
        }

        return true;
    }

    function checkrule($rule, $word)
    {
        if (!empty($rule['r'])) {
            if (!preg_match($rule['r'], $word['value'])) {
                return false;
            }
        }
        return $rule['type'] == $word['type'];
    }

    public $matches = [];


    function isend($i)
    {
        return $i >= count($this->words);
    }

    function getRule()
    {
        return
            [
                'create' => [
                    'matches' => [
                        ['type' => 'white', 'n' => [0, 999], 'name' => 'white1'],
                        ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
                    ]]
            ];
//        [
//
//            'create' => [
//                'matches' => [
//                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => '/CREATE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_create'],
//                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => '/TABLE/', 'type' => 'key', 'n' => [1, 1], 'name' => 'key_table'],
//                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => '/`.*`/', 'type' => 'name2', 'n' => [1, 1], 'name' => 'table_name'],
//                    ['r' => '/\(/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
//                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => 'feild', 'type' => 'call', 'n' => [0, 999], 'name' => 'field'],
//                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => '/\)/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
//                    ['r' => '/\;/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol'],
//                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//
//                ]],
//            'feild' => [
//                'matches' => [
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => '/`.*`/', 'type' => 'name2', 'n' => [1, 1], 'name' => 'field_name'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => '/BIGINT/', 'type' => 'key', 'n' => [1, 1], 'name' => 'keys'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => '/\(/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol_end'],
////                    ['r' => '/[0-9]+/', 'type' => 'int', 'n' => [0, 999], 'name' => 'field'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => '/\)/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol_end'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => '/NOT/', 'type' => 'key', 'n' => [1, 1], 'name' => 'keys'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => '/NULL/', 'type' => 'key', 'n' => [1, 1], 'name' => 'keys'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
////                    ['r' => '/AUTO_INCREMENT/', 'type' => 'key', 'n' => [1, 1], 'name' => 'keys'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//                    ['r' => '/\,/', 'type' => 'symbol', 'n' => [0, 1], 'name' => 'keys'],
////                    ['r' => 'white', 'type' => 'call', 'n' => [0, 999], 'name' => 'white1'],
//
//                ]],
//            'white' => [
//                'matches' => [
//                    ['r' => '/\s+/', 'type' => 'white', 'n' => [0, 999], 'name' => 'white1'],
//                ], 'name' => 'white'],
//            'symbol_end' => [
//                'matches' => [
//                    ['r' => '/\;/', 'type' => 'symbol', 'n' => [1, 1], 'name' => 'symbol_end'],
//                ]
//            ],
//        ];
    }


}


