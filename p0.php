<?php

use LexicalAnalyzer\HardCodeParser;

require_once __DIR__ . '/testhelpers.php';


$p = new \LexicalAnalyzer\Parser(\LexicalAnalyzer\LexRules::getSqlRules());
$s = 'CREATE(FIELD)';

print_r($p->run($s));
