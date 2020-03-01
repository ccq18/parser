<?php

namespace Parser;


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
            $rule['r'] = $r;}

        return $rule;
    }

    protected $rules;
    protected $n;
    protected $name;


    function r($r = null, $type = null, $name = null, $min = 1, $max = 1)
    {
        $this->rules[] = static::g($type, $r, $name, $min, $max);
        return $this;
    }

    function n($min = 1, $max = PHP_INT_MAX)
    {
        $this->n = [$min, $max];
        return $this;
    }

    function after($func)
    {
        $this->func = $func;
        return $this;
    }



    /**
     * @return
     * [
     * 'matches' => [
     * ['type' => $type, 'n' => [$min, $max], 'name'=>]],
     * 'n' => ,
     * 'after'=>
     * ]
     */
    function get()
    {
        return [
            'matches' => $this->rules,
            'n' => $this->n ?? [1, PHP_INT_MAX],
            'after' => $this->func ?? function ($v) {
                    return $v;
                }
        ];
    }


}