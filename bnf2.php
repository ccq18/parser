<?php

require_once __DIR__ . '/testhelpers.php';
//<rule> ::= <identifier> "::=" <expression>
//<expression> ::= <factor> {term}
//<term> ::= "|" <factor>
//<factor> ::= <identifier> | <quoted_symbol> | <expression_c> | <expression_d> | <expression_e>
//<expression_c>::="(" <expression> ")"
//<expression_d>::="[" <expression> "]"
//<expression_e>::="{" <expression> "}"
//<identifier> ::= letter { letter | digit }
//<quoted_symbol> ::= ""
$s = '<rule>::=<identifier>"::="<expression>"::="<hello>
<hello>::=<identifier>"::="<expression><bb>
<aa>::="asds"
';
$lexParser = new \Parser\BnfParser();
$words = $lexParser->parser($s);
print_r($words);
//var_dump(count($rs));
//$analyzer->showLog();