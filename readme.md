## 使用

## 初始化
```
$lexParser = new \LexicalAnalyzer\Parser($lexrules);
$analyzer = new Analyzer($analyzerRules);
        
$words = $lexParser->run($s);
$rs = $analyzer->run($words);
```

# LexicalAnalyzer 词法分析器 

#词法规则结构
```
[
    //一个元素一个  多条
     //  matches是匹配元素的规则，type 是自定义类型，可以任意值
     
    //空白
    ['matches' => [
        //元素中的字符规则，单字符匹配，多字符需要
        //r是正则  n是允许出现的次数
        ['r' => '/[\s]/', 'n' => [1, PHP_INT_MAX]],
       
    ], 'type' => 'white'],
]
```

## 输出   
```
[
[ 'type'=>  和规则中对应的type一致
 'value'=> 匹配到的字符串
 ]  
]
```
# SyntaxAnalyzer 语法分析器 

#语法规则结构
```
 [
 //一个语法规则一条
 'analyzer_name'=>[
      'matches' => [
      //r是正则  n是允许出现的次数 type是匹配词法中对应的类型 ,name是自定义名称, 
      //若type 为call 则r是name的数组，可以引用对应analyzer_name的规则
      ['r','type' => $type, 'n' => [$min, $max], 'name'=>]
      ],
      'n' => ,
      'after'=> //后处理函数，可以不填
      ]
]
```

# Parser 解析器实现 
```
$p = new \Parser\JsonParser();
$ss = [
    '{"a":{"a":"b","c":"333"},"c":"b"}',
    '["a","q"]',
    '{"a":"bb","c":"b"}',
    '{"a":{"a":"b","c":"333"},"c":["a","q"],"d":"222"}'
];
foreach ($ss as $s) {
    $rs = $p->parser($s);
    print_r($rs);
}
```