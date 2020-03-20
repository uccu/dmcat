<?php

namespace App\Resource\Controller;

use Controller;

use Uccu\DmcatTool\Tool\AJAX;
use Uccu\DmcatHttp\Request;
use DB;
use App\Resource\Model\ThemeModel as Theme;
use App\Resource\Model\ResourceModel as Resource;
use fengqi\Hanzi\Hanzi;

class ThemeApi extends Controller{


    function __construct(){


        

    }

    function add(){


        $res = Request::getSingleInstance();
        $info = $res->request(['name','tags','matches','level','season','number','last_number','content','visible']);
        !$info['name'] && AJAX::error('名字空');
        $info['ctime'] = $info['change_time'] = TIME_NOW;
        $info['first'] = substr( Hanzi::pinyin($info['name'])['py'],0,1);
        $data['id'] = Theme::getInstance()->set($info)->add()->lastInsertId;
        if(!$data['id'])AJAX::error('创建失败！');
        AJAX::success($data);
    
    }

    function delete(){
        
        $res = Request::getSingleInstance();
        $id = $res->request('id');

        $data['count'] = Theme::getInstance()->remove($id)->affectedRowCount;
        
        AJAX::success($data);


    }

    function update(){

        
        $res = Request::getSingleInstance();
        $id = $res->request('id');
        if(!$id)AJAX::error('没有ID');
        $info = $res->request(['name','tags','matches','level','season','number','last_number','content','visible']);

        foreach($info as $k=>$v){
            if(is_null($v))unset($info->$k);
        }

        $data['count'] = Theme::getInstance()->set($info)->save($id)->affectedRowCount;
        
        AJAX::success($data);

    }

    function sort($id){

        $theme = Theme::getInstance()->find($id);

        if(!$theme)AJAX::error('主题不存在');

        $data['number'] = $theme->number = Resource::getInstance()->select('sitelink.id')->select(DB::raw('count(sitelink.id) as count'))->where(['theme_id'=>$theme->id,'new_number'=>$theme->last_number])->find()->count;
        
        $data['succ'] = $theme->save()->affectedRowCount;

        AJAX::success($data);

    }
    
    function get($id){

        $info = Theme::getInstance()->find($id);
        !$info && AJAX::error('主题不存在！');

        AJAX::success(['info'=>$info]);
    }


}