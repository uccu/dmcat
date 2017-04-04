<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;
use stdClass;
use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;
use Model;
use App\Resource\Model\ResourceNameSharp as RNS;
use App\Resource\Model\CacheModel as Cache;


use View;


class TestController extends Controller{


    function __construct(){
       
        //var_dump(strnatcasecmp('abc','ABC'));
       
        // $data['info'] = new RNS('龙珠 第100集');

        // AJAX::success($data);

        
        
    }



    function main(Request $request ,Lession $lession ,$baka = 1){

        // var_dump( $request );

        // var_dump( $lession );
        echo "123\n";
 
    }

    function ec(Model $user){

        $z = $user->where([['%F=%d','id',1]])->get();
        
        echo $z;
        
       

        

    }


    function tt(){

        //ignore_user_abort();
        //set_time_limit(1);

        echo strtotime('Sun, 19 Mar 2017 00:17:33 +0800');

    }

    function haml(){

        View::addData(['g'=>['title'=>'zz','keywords'=>'baka']]);

        View::hamlReader('Test/my','App');


    }

    private function zCurl($url,$postData = []){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		if($postData){
            curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $json = curl_exec($ch);
        curl_close($ch);
        return $json;
        
    }


    private function push($title,$link = '',$hash = '',$additional = '',$token = ''){

        if(!$title)return;

        $request['name'] = $title;
        $request['outlink'] = $link;
        $request['hash'] = $hash;
        $request['additional'] = $additional;
        $request['token'] = $token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://h.4moe.com/api/add");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
        $json = curl_exec($ch);

        return $json;
        curl_close($ch);


    }

    private function _typein_addzero($r,$e,$t=4){
		$r=(string)$r;
		$re=strlen($r);
		for($i=0;$i<$t*$e-$re;$i++)$r='0'.$r;
		return $r;
	}
    private function _hashtobase32($hash){
        if(!preg_match('/^[a-z0-9]{40}$/i',$hash))return '';
        $a='abcdefghijklmnopqrstuvwxyz234567';$p='';
		for($i=0;$i<4;$i++)$p .= $this->_typein_addzero(base_convert(substr($hash,$i*10,10),16,2),10);
		$base32='';
		for($i=0;$i+5<=160;$i+=5)$base32.=$a[base_convert(substr($p,$i,5),2,10)];
		return strtoupper($base32);
    }
    private function _base32tohash($base32){
        if(!preg_match('/^[a-z2-7]{32}$/i',$base32))return '';
        $a='abcdefghijklmnopqrstuvwxyz234567';
		$str='';
		for($i=0;$i<32;$i++)$str.=(string)($this->_typein_addzero(decbin(stripos($a,$base32[$i])),1,5));
		$hash='';
		for($i=0;$i+4<=40*4;$i+=4)$hash.=base_convert(substr($str,$i,4),2,16);
		return $hash;
    }



    function img(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://i3.pixiv.net/c/1200x1200/img-master/img/2013/09/20/17/53/12/38631998_p0_master1200.jpg");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        $headers = array();
        $headers[] = 'Accept:image/webp,image/*,*/*;q=0.8';
        $headers[] = 'Accept-Encoding:gzip, deflate, sdch, br';
        $headers[] = 'Accept-Language:zh-CN,zh;q=0.8,en;q=0.6';
        $headers[] = 'Connection:keep-alive';
        $headers[] = 'Host:i3.pixiv.net';
        $headers[] = 'Referer:http://www.pixiv.net/';
        $headers[] = 'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36';

        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $z = curl_exec($ch);
        //$a = curl_getinfo($ch);
        
        header('Content-type: image/jpeg');
        
        curl_close($ch);
        echo $z;
    }

    function moe(){

        // header('content-type:text/xml; charset=utf-8');

        $str = $this->zCurl('https://bangumi.moe/rss/latest');

        // $str = file_get_contents(BASE_ROOT.'test.xml');

        $objz = simplexml_load_string($str);

        $it = [];

        $cache = Cache::getInstance();
        $lastPubdate = $cache->cget('last_data_moe_pubdate');

        $count = count($objz->channel->item);

        $k = 0;
        foreach($objz->channel->item as $item){
            if(!$item)break;
            $obj = new stdClass();

            $obj->title = $item->title.'';
            $obj->link = $item->link.'';
            $obj->additional = str_replace('https://bangumi.moe/torrent/','',$obj->link);
            $obj->time = strtotime($item->pubDate);
            if($obj->time <= $lastPubdate)break;
            $it[$k] = $obj;
            $k++;
        }

        $it = array_reverse($it);
        echo $length = count($it);

        foreach($it as $k=>$v){

            echo $this->push($v->title,$v->link,'',$v->additional,'7811ade5a1dfa34b4d18352070737cc02f191424');


            $cache->csave('last_data_moe_pubdate',$v->time);
            
            if($k+1!=$length){
                $rand = rand(0,floor(600/$length));
                sleep($rand);
            }
        }


    }


    function tucao(){

        // header('content-type:text/xml; charset=utf-8');

        $str = $this->zCurl('http://www.tucao.tv/play/r11/');

        // $str = file_get_contents(BASE_ROOT.'test.xml');

        $objz = simplexml_load_string($str);

        $it = [];

        $cache = Cache::getInstance();
        $lastPubdate = $cache->cget('last_data_tucao_pubdate');

        $count = count($objz->channel->item);

        $k = 0;
        foreach($objz->channel->item as $item){
            if(!$item)break;
            $obj = new stdClass();

            $obj->title = $item->title.'';
            $obj->link = $item->link.'';
            $obj->additional = strtotime($item->pubDate);
            if($obj->additional <= $lastPubdate)break;
            $it[$k] = $obj;
            $k++;
        }

        $it = array_reverse($it);
        echo $length = count($it);

        foreach($it as $k=>$v){

            echo $this->push($v->title,$v->link,'',$v->additional,'71B27A6E921B7BFABA69040AA3F4A9A3B13E4759');


            $cache->csave('last_data_tucao_pubdate',$v->additional);
            
            if($k+1!=$length){
                $rand = rand(0,floor(600/$length));
                sleep($rand);
            }
        }


    }
    function dmhy(){

        // header('content-type:text/xml; charset=utf-8');

        $str = $this->zCurl('https://share.dmhy.org/topics/rss/sort_id/2/rss.xml');

        // $str = file_get_contents(BASE_ROOT.'test.xml');

        $objz = simplexml_load_string($str);

        $it = [];

        $cache = Cache::getInstance();
        $lastPubdate = $cache->cget('last_data_dmhy_pubdate');

        $count = count($objz->channel->item);

        $k = 0;
        foreach($objz->channel->item as $item){
            if(!$item)break;
            $obj = new stdClass();

            $obj->title = $item->title.'';
            $obj->link = $item->link.'';
            $base32 = substr($item->enclosure->attributes()['url'].'',20,32);
            $obj->hash = $this->_base32tohash($base32);
            $obj->additional = strtotime($item->pubDate);
            if($obj->additional <= $lastPubdate)break;
            $it[$k] = $obj;
            $k++;
        }

        $it = array_reverse($it);
        echo $length = count($it);

        foreach($it as $k=>$v){

            echo $this->push($v->title,$v->link,$v->hash,$v->additional,'128A39B92199C8B774489D1E4732FEB36C4777A2');


            $cache->csave('last_data_dmhy_pubdate',$v->additional);
            
            if($k+1!=$length){
                $rand = rand(0,floor(600/$length));
                sleep($rand);
            }
        }


    }


    function bili(Cache $cache){

        global $argc;
        global $argv;
        if(!$argc)AJAX::error('请在shell运行');
        
        ignore_user_abort();
        set_time_limit(600);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bangumi.bilibili.com/index/new/33.json');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $json = curl_exec($ch);
        $json=gzdecode($json);
        curl_close($ch);
        if($json)$json = json_decode($json);
        else{
            echo 'error';
            return;
        };
        
        $json = $json->list;$array = [];
        $lastDataPubdate = $cache->cget('last_data_bili_pubdate');
        foreach($json as $k=>$v)if($v->pubdate>$lastDataPubdate){
            $array[] = $v;
        }else break;
        
        ksort($array);
        $length = count($array);
        foreach($array as $k=>$data){
            $request = [];
            $request['name'] = $data->title;

            $request['outlink'] = 'http://www.bilibili.com/video/av'.$data->aid;
            $request['additional'] = $data->aid;
            $request['token'] = '860F3ABBWEB7F30FAD15EEEF6BA6A07D3386AB8A';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://h.4moe.com/api/add");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
            //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $json = curl_exec($ch);

            echo $json;
            curl_close($ch);

            $cache->csave('last_data_bili_pubdate',$data->pubdate);
            if($k+1!=$length){
                $rand = rand(0,floor(600/$length));
                sleep($rand);
                // die();
            }
            
        }

        
    }

    function acgnx(Cache $cache){

        global $argc;
        global $argv;
        if(!$argc)AJAX::error('请在shell运行');
        
        ignore_user_abort();
        set_time_limit(600);

        $json = $this->zCurl('https://open.acgnx.se/json-1-sort-1.json');
        

        if($json)$json = json_decode($json,true);
        else{
            echo 'error';
            return;
        };

        $json = $json['item'];$array = [];
        $lastDataId = $cache->cget('last_data_id');
        foreach($json as $k=>$v)if($v['data_id']>$lastDataId)$array[$v['data_id']] = $v;

        ksort($array);
        echo $length = count($array);
        echo ',';
        foreach($array as $k=>$data){
            

            echo $json = $this->push(
                $data['title'],
                'https://share.acgnx.se/show-'.$data['hash_id'].'.html',
                $data['hash_id'],
                $data['data_id'],
                '860F3ABB7EB7E30FAD15EEEF6BA6A07D3386AB8A'
            );


            $cache->csave('last_data_id',$data['data_id']);
            if($k+1!=$length){
                $rand = rand(0,floor(600/$length));
                sleep($rand);

            }
        }
        

    }

    

    function pull(){

        system("cd ".BASE_ROOT." && git pull");
        
    }


}
