<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;
use Request;
use StdClass;
use App\Resource\Model\ThemeModel as Theme;


class ThemeApi extends Controller{


    function __construct(){


        

    }

    function add(){


        $res = Request::getInstance();
        $info = $res->request(['name','tags','matches','level','season','number','last_number','content','visible']);

        $data['id'] = Theme::getInstance()->set($info)->add()->getStatus();
        
        AJAX::success($data);
    
    }

    function delete(){
        
        $res = Request::getInstance();
        $id = $res->request('id');

        $data['count'] = Theme::getInstance()->remove($id)->getStatus();
        
        AJAX::success($data);


    }

    function update(){

        $info = new stdClass;
        $res = Request::getInstance();
        $id = $res->request('id');
        if(!$id)AJAX::error('没有ID');
        $info = $res->request(['name','tags','matches','level','season','number','last_number','content','visible']);

        foreach($info as $k=>$v){
            if(is_null($v))unset($info->$k);
        }

        $data['count'] = Theme::getInstance()->set($info)->save($id)->getStatus();
        
        AJAX::success($data);

    }



}