<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;

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

        echo date('w');

    }

    function haml(){

        View::addData(['g'=>['title'=>'zz','keywords'=>'baka']]);

        View::hamlReader('Test/my','App');


    }

    function curl(Cache $cache){

        global $argc;
        global $argv;
        if(!$argc)AJAX::error('请在shell运行');
        
        ignore_user_abort();
        set_time_limit(600);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://open.acgnx.se/json-1-sort-1.json");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $json = curl_exec($ch);
        curl_close($ch);

        if($json)$json = json_decode($json,true);
        else{
            echo 'error';
            return;
        };

        $json = $json['item'];$array = [];
        $lastDataId = $cache->cget('last_data_id');
        foreach($json as $k=>$v)if($v['data_id']>$lastDataId)$array[$v['data_id']] = $v;

        ksort($array);
        $length = count($array);
        foreach($array as $k=>$data){
            $request = [];
            $request['name'] = $data['title'];
            $request['hash'] = $data['hash_id'];
            $request['outlink'] = 'https://share.acgnx.se/show-'.$data['hash_id'].'.html';
            $request['additional'] = $data['data_id'];
            $request['token'] = '860F3ABB7EB7E30FAD15EEEF6BA6A07D3386AB8A';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://c.baka/api/add");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
            //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $json = curl_exec($ch);

            echo $json;
            curl_close($ch);

            $cache->csave('last_data_id',$data['data_id']);
            if($k+1!=$length){
                $rand = rand(0,floor(600/$length));
                sleep($rand);
                // die();
            }
        }
        

    }

    function apitest(){

        global $argc;
        global $argv;
        if(!$argc)AJAX::error('请在shell运行');
        
        ignore_user_abort();
        set_time_limit(600);


        $json = '{
            "data_id": 469350,
            "hash_id": "b4dsedb3d024e382ae3e5b56b81b0db5cfcf8031",
            "title": "[c.c动漫][10月新番][时间飞船24][Time Bokan 24][12][GB][720P][MP4][网盘]",
            "sort_id": 1,
            "sort_name": "動畫",
            "timestamp": 1482336120,
            "updateusername": "動漫花園鏡像",
            "updateuserid": 3
        }';

        $data = json_decode($json,true);
        
        $request = [];
        $request['name'] = $data['title'];
        $request['hash'] = $data['hash_id'];
        $request['outlink'] = 'https://share.acgnx.se/show-'.$data['hash_id'].'.html';
        $request['additional'] = $data['data_id'];
        $request['token'] = '860F3ABB7EB7E30FAD15EEEF6BA6A07D3386AB8A';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://c.baka/api/add");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
        $json = curl_exec($ch);
        echo $json;
        curl_close($ch);

        
        

    }


}