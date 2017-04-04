<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;
use Request;
use StdClass;
use App\Resource\Model\ThemeModel as Theme;
use App\Resource\Model\ResourceModel as Resource;
use fengqi\Hanzi\Hanzi;

class ThemeApi extends Controller{


    function __construct(){


        

    }

    function add(){


        $res = Request::getInstance();
        $info = $res->request(['name','tags','matches','level','season','number','last_number','content','visible']);
        
        !$info['name'] && AJAX::error('名字空');

        $info['first'] = substr( Hanzi::pinyin($info['name'])['py'],0,1);

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

    function sort($id){

        $theme = Theme::getInstance()->find($id);

        if(!$theme)AJAX::error('主题不存在');

        $data['number'] = $theme->number = Resource::getInstance()->select(['count(%F) as count','sitelink.id'],'RAW')->where(['theme_id'=>$theme->id,['new_number'=>$theme->last_number]])->find()->count;
        
        $data['succ'] = $theme->save()->getStatus();

        AJAX::success($data);

    }
    



}