<?php


//根据规则生成
namespace LexicalAnalyzer;
class Parser
{

    protected $words;
    protected $i;
    protected $s;


    protected $rules = null;

    function __construct($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param $s
     * @return array [['type'=> 'value'=>]]
     */
    function run($s)
    {
        $this->i = 0;
        $this->s = $s;
        $this->words = [];
        while (!$this->isend()) {
            if (!$this->match()) {
                exit('匹配失败' . $this->s[$this->i]);
            };
        }
        return $this->words;
    }


    function match()
    {

        // 匹配规则1， 匹配上 则命中
        $m = true;
        $matchRule = null;
        $i0 = null;
        foreach ($this->rules as $rule) {
            $i0 = $this->i;
            $m = true;
            //任一一条匹配不上则匹配失败
            foreach ($rule['matches'] as $r) {
                $n = 0;
                while (!$this->isend() && preg_match($r['r'], $this->s[$this->i])) {
                    $this->i++;
                    $n++;
                    //超过最大匹配次数则跳出当前
                    if(isset($r['n'][1])){
                        if ($n == $r['n'][1]) {
                            break;
                        }
                    }

                }
                if (!($n >= $r['n'][0])) {
                    $m = false;
                    $this->i = $i0;
                    break;
                }
            }
            //匹配成功则跳出
            if ($m) {
                $this->words[] = ['type' => $rule['type'], 'value' => substr($this->s, $i0, $this->i - $i0)];
                break;
            }
        }
        //匹配成功则保存

        return $m;
    }

    function isend()
    {
        return $this->i >= strlen($this->s);
    }

}
