<?php

namespace App\Doowin\Controller;
use Controller;
use Request;
use Response;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;


use App\Doowin\Model\NewsGroupModel;
use App\Doowin\Model\NewsHotModel;
use App\Doowin\Model\NewsMediaModel;
use App\Doowin\Model\NewsVideoModel;
use App\Doowin\Model\NewsVideoTypeModel;


require_once(BASE_ROOT.'App/Doowin/Middleware/Lang.php');

class AppHomeNewsController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        
    }

    function inNews(NewsGroupModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '集团要闻';
        $limit = 16;
        $list = $model->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        if($page == 1){
            $first = $list[0];
            unset($list[0]);
        }
        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    function newsInfo(NewsGroupModel $model,$id = 0){

        $type = 'inNews';
        $name = '集团要闻';
        $info = $model->find($id);
        $info->browse++;
        $info->save();
        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    function getPageLink($a,$b,$c='',$d = 16){

        return Func::getPageLink($a,$b,$c,$d);
    }
    function special(NewsHotModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '热点专题';
        $limit = 16;
        $list = $model->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    function specialInfo(NewsHotModel $model,$id = 0){

        $type = 'special';
        $name = '热点专题';
        $info = $model->find($id);
        !$info && Response::getInstance()->r302('/404.html');
        $info->browse++;
        $info->save();
        include_once(VIEW_ROOT.'App/News_newsInfo.php');

    }
    function media(NewsMediaModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '媒体聚焦';
        $limit = 16;
        $list = $model->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    function mediaInfo(NewsMediaModel $model,$id = 0){

        $type = 'media';
        $name = '媒体聚焦';
        $info = $model->find($id);
        !$info && Response::getInstance()->r302('/404.html');
        $info->browse++;
        $info->save();
        include_once(VIEW_ROOT.'App/News_newsInfo.php');

    }
    function video(){
        
        $type = __FUNCTION__;
        $name = '视频中心';
        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    
    
}
