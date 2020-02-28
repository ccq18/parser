<?php

use LexicalAnalyzer\Parser;
use SyntaxAnalyzer\Analyzer;

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
$s = '<rule>::=<identifier>"::="<expression>
<rule>::=<identifier>"::="<expression>
';
$lexParser = new Parser(\Parser\BnfParser::getLexRule());
$words = $lexParser->run($s);
//print_r($words);
$analyzer = new Analyzer(\Parser\BnfParser::getAnalyzerRules());
$rs = $analyzer->run($words);
//print_r($rs);
var_dump(count($rs));
//$analyzer->showLog();