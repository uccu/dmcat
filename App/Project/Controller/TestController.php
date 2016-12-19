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
        
 
    }

    function ec(Model $user){

        $z = $user->where([['%F=%d','id',1]])->get();
        
        echo $z;
        
       

        

    }


    function getLessionById($name = null,$id = null){

        //var_dump(func_get_args());
        //echo '123';

        echo Lession::getInstance()->where('id=%d',1)->get();

    }

    function haml(){

        View::hamlReader('Test/my','App');


    }

    function curl(Cache $cache){

        global $argc;
        global $argv;
        if(!$argc)AJAX::error('请在本地运行');
        
        ignore_user_abort();
        set_time_limit(600);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://open.acgnx.se/json-1-sort-1.json");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        $json = curl_exec($ch);
        curl_close($ch);

        if($json)$json = json_decode($json,true);
        else return;

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
            $request['token'] = 'S3Q3FFfvq3r35V3';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://d.baka/api/add");
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
                // sleep($rand);
                //die();
            }
        }
        

    }



}