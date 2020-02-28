<?php

use LexicalAnalyzer\SimpleParser;

require_once __DIR__ . '/testhelpers.php';

// echo "\xE6\x89\x8B\xE6\x9C\xBA\xE6\x8D\xA2\xE6\x96\xB0\xE6\xAC\xBE";
$s = "<p>11</p>  sassa 222;;';''";
$p = new SimpleParser();
$p->run($s);
