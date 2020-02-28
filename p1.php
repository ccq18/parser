<?php

use LexicalAnalyzer\HardCodeParser;

require_once __DIR__ . '/testhelpers.php';


$p = new HardCodeParser();
$p->i = 0;
$s = 'aaa;aa"aaaa"';

print_r($p->run($s));
