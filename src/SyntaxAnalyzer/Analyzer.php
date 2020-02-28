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
                if($rule['n'][1] == 0){
                    continue;
                }
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
//                    var_dump($r);
                    $this->log('match', [$w, $r['r'] ?? "", false,$n, $r['n'][0]]);
                    return false;
                } else {
//                    var_dump($r);
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
            //不是正则则匹配全等
            if($rule['r'][0]!='/'){
                if($rule['r'] != $word['value']){
                    return false;
                }
                //正则匹配调用正则
            }else if (!preg_match($rule['r'], $word['value'])) {
                return false;
            }
        }
        if (!empty($rule['type'])) {
            if ($rule['type'] != $word['type']) {
                return false;
            }
        }
        return true;
    }

    public $matches = [];


    function isend($i)
    {
        return $i >= count($this->words);
    }

}


