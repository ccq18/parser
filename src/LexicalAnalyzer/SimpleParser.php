<?php


namespace LexicalAnalyzer;
class SimpleParser
{

    private $status;
    private $tag;
    private $str;
    private $strings = [];
    private $statuses = [];

    private $i;


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
        $this->end();
        $this->status = static::STRING_IN;

        print_r($this->strings);
        print_r($this->statuses);
        return $this->strings;
    }

    const NONE = 'NONE';
    const STRING_IN = 'STRING_IN';

    const TAG_IN = 'TAG_IN';

    const TAG_OUT = 'TAG_OUT';


// ssss<p>sss</p> ;
    function end()
    {
        if ($this->status == static::NONE) {

        } elseif ($this->status == static::STRING_IN) {
            //STRING_IN end
            $this->strings[] = ['type' => 'string', 'content' => $this->str];
        } else {
            exit("运行出错");
        }
    }

    function charin($c)
    {
        if ($this->status == static::NONE) {
            if ($c == '<') {
                //TAG_IN
                $this->tag = '';
                $this->status = static::TAG_IN;

            } else {
//              NORMAL_EAT
                $this->str = '';;
                $this->str .= $c;
                $this->status = static::STRING_IN;
            }

        } elseif ($this->status == static::STRING_IN) {
            if ($c == '<') {
                //STRING_IN end
                $this->strings[] = ['type' => 'string', 'content' => $this->str];
                //TAG_IN
                $this->tag = '';
                $this->status = static::TAG_IN;

            } else {
//                NORMAL_EAT
                $this->str .= $c;;
            }

        } elseif ($this->status == static::TAG_IN) {
            if ($c == '>') {
                //TAG_IN_END
                $this->strings[] = ['type' => 'tag', 'name' => $this->tag];
                //NORMAL
                $this->status = static::NONE;
            } else {
                //TAG_IN_EAT
                $this->tag .= $c;
//                $this->event(static::TAG_IN_EAT, $c);
            }

        }


    }


}
