<?php
require_once __DIR__ . '/vendor/autoload.php';
function testRule($r, $ycases, $ncase, $show = true,$method=null)
{
    if($method == null){
        $method = function ($rule,$data){
            return preg_match($rule,$data);
        };
    }


    out("\nr:{$r}", 'info', $show);
    foreach ($ycases as $v) {
        out("\nycase " . $v, 'info', $show);
        if ($method($r, $v)) {
            out('true', 'success', $show);

        } else {
            out('false', 'error', $show);
        };

    }
    foreach ($ncase as $v) {
        out("\nncase " . $v, 'info', $show);

        if ($method($r, $v)) {
            out('false', 'error', $show);
        } else {
            out('true', 'success', $show);

        };

    }
}

function out($text, $color = null, $show = true)
{
    if (!$show) {
        return;
    }
    $styles = array(
        'success' => "\033[0;32m%s\033[0m",
        'error' => "\033[31;31m%s\033[0m",
        'info' => "\033[33;33m%s\033[0m"
    );

    $format = '%s';
    if (isset($styles[$color])) {
        $format = $styles[$color];
    }


    printf($format, $text);
}

function error($str, $exit = false)
{
    out($str, 'error');
    if ($exit) {
        exit;
    }
}


