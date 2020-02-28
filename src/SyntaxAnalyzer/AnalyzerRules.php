<?php

namespace SyntaxAnalyzer;


class AnalyzerRules
{

    static function one()
    {
        return new AnalyzerRules();
    }


    public static function g($type, $r = null, $name = null, $min = 1, $max = 1)
    {
        $rule = ['type' => $type, 'n' => [$min, $max], 'name' => $name ?? '_' . uniqid()];
        if (!empty($r)) {
            $rule['r'] = $r;
        }

        return $rule;
    }

    protected $rules;
    protected $n;
    protected $name;

    function r($type, $r = null, $name = null, $min = 1, $max = 1)
    {
        $this->rules[] = static::g($type, $r, $name, $min, $max);
        return $this;
    }

    function n($min = 1, $max = 999)
    {
        $this->n = [$min, $max];
        return $this;
    }

    function end($func)
    {
        $this->func = $func;
        return $this;
    }

    function after()
    {

    }

    /**
     * @return
     * [
     * 'matches' => [['type' => $type, 'n' => [$min, $max], 'name'=>]],
     * 'n' => ,
     * 'after'=>
     * ]
     */
    function get()
    {
        return [
            'matches' => $this->rules,
            'n' => $this->n ?? [1, 999],
            'after' => $this->func ?? function ($v) {
                    return $v;
                }
        ];
    }


}