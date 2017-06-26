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
    function inNewsSearch(NewsGroupModel $model,$page = 1){

        $search = Request::getInstance()->cookie('search','');
        $type = 'inNews';
        $name = '集团要闻';
        $limit = 16;
        $search && $where['title'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];
        
        $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        if($page == 1){
            $first = $list[0];
            unset($list[0]);
        }
        include_once(VIEW_ROOT.'App/News_'. 'inNews' .'.php');

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
    function specialSearch(NewsHotModel $model,$page = 1){
        
        $type = 'special';
        $name = '热点专题';
        $limit = 16;
        $search = Request::getInstance()->cookie('search','');
        $search && $where['title'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];
        $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'App/News_'. 'special' .'.php');

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
    function mediaSearch(NewsMediaModel $model,$page = 1){
        
        $type = 'media';
        $name = '媒体聚焦';
        $limit = 16;
        $search = Request::getInstance()->cookie('search','');
        $search && $where['title'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];
        $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'App/News_'. 'media' .'.php');

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
    function video(NewsVideoTypeModel $newsVideoTypeModel,NewsVideoModel $newsVideoModel,$page = 1){
        
        $type = __FUNCTION__;
        $name = '视频中心';
        $limit = 16;
        $video_type = Request::getInstance()->cookie('video_type',0);
        if($video_type)$where = ['type'=>$video_type];
        $newsVideoType = $newsVideoTypeModel->order('ord','id')->get()->toArray();
        $newsVideo = $newsVideoModel->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $where2 = $where;
        $where2['banner'] = 1;
        $banner = $newsVideoModel->where($where2)->order('top desc','id desc')->get()->toArray();
        $max = $newsVideoModel->select('COUNT(*) as c','RAW')->where($where2)->find()->c;

        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    function videoSearch(NewsVideoTypeModel $newsVideoTypeModel,NewsVideoModel $newsVideoModel,$page = 1){
        
        $type = 'video';
        $name = '视频中心';
        $limit = 16;
        $video_type = Request::getInstance()->cookie('video_type',0);
        if($video_type)$where = ['type'=>$video_type];
        $newsVideoType = $newsVideoTypeModel->order('ord','id')->get()->toArray();
        $newsVideo = $newsVideoModel->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $where2 = $where;
        $where2['banner'] = 1;
        $search = Request::getInstance()->cookie('search','');
        $search && $where3['title'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];
        $banner = $newsVideoModel->where($where2)->where($where3)->order('top desc','id desc')->get()->toArray();
        $max = $newsVideoModel->select('COUNT(*) as c','RAW')->where($where2)->where($where3)->find()->c;

        include_once(VIEW_ROOT.'App/News_'. 'video' .'.php');

    }
    function videoPlay(NewsVideoModel $newsVideoModel,$id = 0){
        
        
        
        $info = $newsVideoModel->find($id);

        $newsVideo = $newsVideoModel->order('rand()','RAW')->limit(4)->get()->toArray();
        

        include_once(VIEW_ROOT.'App/News_'. __FUNCTION__ .'.php');

    }
    
}
