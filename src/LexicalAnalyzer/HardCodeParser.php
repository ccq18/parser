<?php

namespace LexicalAnalyzer;
class HardCodeParser
//纯手写
{

    private $status;
    private $key;
    private $str;
    private $str_start;
    public $strings = [];
    public $statuses = [];

    public $i;


    public function run($s)
    {
        $this->status = static::NONE;
        $len = strlen($s);
        /**
         * 遍历字符串
         */
        for ($this->i = 0; $this->i < $len; $this->i++) {
            $c = $s[$this->i];

            $this->charin($c);
            $this->statuses[] = [$c, $this->status];
        }
        if ($this->status == static::KEY_IN) {
            $this->strings[] = ['type' => 'key', 'content' => $this->key];
            $this->status = static::NONE;
        }
        if ($this->status != static::NONE) {
            error("运行出错:" . $this->status, true);
        }
        $this->status = static::STRING_IN;


        return $this->strings;
    }

    // sql
// key string  Symbol 逗号 分号 括号
    const NONE = 'NONE';
    const STRING_IN = 'STRING_IN';
    const SYMBOL_IN = 'SYMBOL_IN';
    const KEY_IN = 'KEY_IN';

    function charin($c)
    {

        if ($this->status == static::NONE) {
            if (preg_match('/[a-zA-Z0-9]/i', $c)) {
                //key
                $this->key = $c;
                $this->status = static::KEY_IN;

            } elseif (preg_match('/[;,\(\)]/i', $c)) {
                //symbol
                $this->strings[] = ['type' => 'symbol', 'content' => $c];
                $this->status = static::NONE;
            } else if (in_array($c, [' ', "\n", "\r"])) {
                //none
                $this->status = static::NONE;
            } else if (in_array($c, ['"', '\''])) {
                //string
                $this->status = static::STRING_IN;
                $this->str_start = $c;
            } else {
                error("\n无法识别的字符:" . $c, true);
            }
        } elseif ($this->status == static::KEY_IN) {
            if (preg_match('/[a-zA-Z0-9]/i', $c)) {
                //key
                $this->key .= $c;
                $this->status = static::KEY_IN;
            } elseif (in_array($c, [';', ',', '(', ')'])) {
                //symbol
                $this->strings[] = ['type' => 'key', 'content' => $this->key];
                $this->strings[] = ['type' => 'symbol', 'content' => $c];
                $this->status = static::NONE;
            } else if (in_array($c, [' ', "\n", "\r"])) {
                //none
                $this->strings[] = ['type' => 'key', 'content' => $this->key];
                $this->status = static::NONE;
            } else if (in_array($c, ['"', '\''])) {
                $this->strings[] = ['type' => 'key', 'content' => $this->key];
                //string
                $this->status = static::STRING_IN;
                $this->str_start = $c;
            } else {
                error("\n无法识别的字符:" . $c, true);
            }


        } elseif ($this->status == static::STRING_IN) {
            if ($this->str_start == $c) {
                //STRING_IN end
                $this->strings[] = ['type' => 'string', 'content' => $this->str];
                //TAG_IN
                $this->status = static::NONE;

            } else {
//                NORMAL_EAT
                $this->str .= $c;;
            }

        }


    }


}
