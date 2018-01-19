<?php
namespace App\Project\Controller;
use Config;
use AJAX;
use View;
use Controller;

class DocController  extends Controller{
   /**
    * @source http://www.jb51.net/article/84048.htm
    */
    private function _format_json($json, $html = true) {

        $json = preg_replace('#\\\/#','/',$json);
        $json = preg_replace('#<#','&lt;',$json);
        $json = preg_replace('#>#','&gt;',$json);

        $json = preg_replace_callback('#\\\u\w\w\w\w#',function($e){
            $t = json_decode('{"d":"'.$e[0].'"}');
            return $t->d;
        },$json);
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;
        if ($html) {
            $tab = "　　";
            $newline = "<br/>";
        } else {
            $tab = "\t";
            $newline = "\n";
        }
            for($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];
            
            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
                } else {
                    switch($char) {
                    case '{':
                        $tabcount++;
                        $result .= $char . $newline . str_repeat($tab, $tabcount);
                        break;
                    case '[':
                        $tabcount++;
                        $result .= $char . $newline . str_repeat($tab, $tabcount);
                        break;
                    case '}':
                        $tabcount--;
                        $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        break;
                    case ']':
                        $tabcount--;
                        $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        break;
                    case ',':
                        $result .= $char . $newline . str_repeat($tab, $tabcount);
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote) $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }
        return $result;
    }
    
    private function _format($name){

        $path = BASE_ROOT.'App/Project/Doc/'.$name;
        $file = fopen($path, "r");
        // echo $path;die();
        $ss = 0;$n = -1;
        $data = [];
        while(!feof($file)) {

            $line = fgets($file);
            $line = trim( preg_replace('/#.*$/','',$line) );
            if(!$line)continue;

            if($ss == 0){
                $n++;
                $ss = 1;
                $data[$n]['note'] = $line;
                continue;
            }elseif($ss == 1){
                $ss = 2;
                $data[$n]['url'] = $line;
                continue;
            }elseif($ss == 2){
                if(substr($line,0,1) != '{'){
                    list($a,$b) = explode(' ',$line);
                    $data[$n]['parameter'][$a] = $b?$b:'';
                    continue;
                }else{
                    $ss = 3;
                    $data[$n]['return'] = $this->_format_json($line);
                    continue;
                }
            }elseif($ss == 3){
                $ss = 0;
                $data[$n]['message'] = $this->_format_json($line);
                continue;
            }
        }

        return $data;
    }

    
    function it(){

        $data = $this->_format(__FUNCTION__);
        View::addData(['data'=>$data,'g'=>['title'=>__FUNCTION__]]);
        View::hamlReader('M','Doc');

    }



    function captcha(){
        $note = '图片验证码';
        $url = 'tool/captcha';

        $parameter = [

            '_otherMessage'=>'',

        ];

        $output = [];
        

    }


    function test(){


        View::hamlReader('Test','Doc');

    }



}