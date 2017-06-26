<?php

namespace App\Doowin\Controller;
use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;
use Response;
use App\Doowin\Model\HomeBannerModel;
use App\Doowin\Model\NewsGroupModel;
use App\Doowin\Model\NewsHotModel;
use App\Doowin\Model\NewsMediaModel;
use App\Doowin\Model\NewsVideoModel;
use App\Doowin\Model\HomeMModel;
use App\Doowin\Model\NewsVideoTypeModel;

use App\Doowin\Model\StaticPageModel;
use App\Doowin\Model\IntroductionProductModel;
use App\Doowin\Model\DevelopModel;
use App\Doowin\Model\ChairmanPictureModel;
use App\Doowin\Model\HonorModel;
use App\Doowin\Model\CharitableModel;
use App\Doowin\Model\MagazineModel;
use App\Doowin\Model\NewspaperModel;

require_once(BASE_ROOT.'App/Doowin/Middleware/Lang.php');

class MobileController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        
    }

    function index(
        HomeBannerModel $bannerModel,
        NewsGroupModel $newsGroupModel,
        NewsHotModel $newsHotModel,
        NewsMediaModel $newsMediaModel,
        NewsVideoModel $newsVideoModel,
        HomeMModel $homeMModel
        ){

        $banner = $bannerModel->order('ord','id')->get()->toArray();
        $newsGroup = $newsGroupModel->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsHot = $newsHotModel->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsMedia = $newsMediaModel->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsVideo = $newsVideoModel->limit(2)->order('top desc','id desc')->get()->toArray();
        $homeM = $homeMModel->limit(5)->order('id')->get()->toArray();
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

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
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }
    function newsInfo(NewsGroupModel $model,$id = 0){

        $type = 'inNews';
        $name = '集团要闻';
        $info = $model->find($id);
        $info->browse++;
        $info->save();
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

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
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }
    function specialInfo(NewsHotModel $model,$id = 0){

        $type = 'special';
        $name = '热点专题';
        $info = $model->find($id);
        !$info && Response::getInstance()->r302('/404.html');
        $info->browse++;
        $info->save();
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }
    function media(NewsMediaModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '媒体聚焦';
        $limit = 16;
        $list = $model->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }
    function mediaInfo(NewsMediaModel $model,$id = 0){

        $type = 'media';
        $name = '媒体聚焦';
        $info = $model->find($id);
        !$info && Response::getInstance()->r302('/404.html');
        $info->browse++;
        $info->save();
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

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
        $max = $newsVideoModel->select('COUNT(*) as c','RAW')->where($where)->find()->c;

        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }
    function videoPlay(NewsVideoModel $newsVideoModel,$id = 0){
        
        
        
        $info = $newsVideoModel->find($id);

        $newsVideo = $newsVideoModel->order('rand()','RAW')->limit(4)->get()->toArray();
        

        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }


    function profile(StaticPageModel $pageModel,IntroductionProductModel $introductionProductModel){
        
        $type = __FUNCTION__;

        $page = $pageModel->find(1);
        $introductionProduct = $introductionProductModel->order('top desc','id desc')->get()->toArray();

        include_once(VIEW_ROOT.'Mobile/'.__FUNCTION__.'.php');

    }
    function chairman(StaticPageModel $pageModel,ChairmanPictureModel $chairmanPictureModel){
        
        $type = __FUNCTION__;

        $page = $pageModel->find(2);

        $chairmanPicture = $chairmanPictureModel->order('year desc','month desc')->get()->toArray();

        foreach($chairmanPicture as &$v){

            if($v->pic){
                $pics = explode(';',$v->pic);
                $v->pic2Array = $pics;
                $v->count = count($v->pic2Array);
                foreach($pics as &$v2)$v2 = Func::fullPicAddr( $v2 );
                $v->picArray = implode(';',$pics);
                $v->first = $v->pic2Array[0];
            }

        }


        include_once(VIEW_ROOT.'Mobile/'.__FUNCTION__.'.php');

    }
    

    function develop($year = 0,DevelopModel $model){
        $type = __FUNCTION__;
        $yearM = date('Y');
        $year = floor($year);
        if(!$year)$year = $yearM;

        $years = $model->distinct()->order('year desc')->get_field('year');


        $where['year'] = $year;
        $list = $model->where($where)->order('month desc')->get()->toArray();
        include_once(VIEW_ROOT.'Mobile/'.__FUNCTION__.'.php');

    }
    function culture(StaticPageModel $pageModel,MagazineModel $magazineModel,NewspaperModel $newspaperModel){
        $type = __FUNCTION__;
        $page = $pageModel->find(3);

        // $magazine = $magazineModel->where(['year'=>date('Y')])->order('top desc')->get()->toArray();
        $newspaper = $newspaperModel->order('id')->get()->toArray();


        include_once(VIEW_ROOT.'Mobile/'.__FUNCTION__.'.php');
    }
    function magazine($year = 0,MagazineModel $magazineModel){

        $magazine = $magazineModel->where(['year'=>$year])->order('top desc')->get()->toArray();
        foreach($magazine as $v){
            echo '<a href="/download/watch?id='.$v->down.'" target="_blank"><div class="newspaper-one">
                <img src="/pic/'.$v->pic.'">
                <h1>'.langV($v,'title').'</h1>
                <h2>'.langV($v,'small').'</h2>
                <h3>'.langV($v,'red').'</h3>
                <h4>'.langV($v,'description').'</h4>
            </div></a>';
        }

    }
    function honor($year = 0,HonorModel $model){
        $type = __FUNCTION__;
        $yearM = date('Y');
        $year = floor($year);
        if(!$year)$year = $yearM;
        $years = $model->distinct()->order('year desc')->get_field('year');
        $where['year'] = $year;
        $list = $model->where($where)->order('month desc')->get()->toArray();
        include_once(VIEW_ROOT.'Mobile/'.__FUNCTION__.'.php');

    }

    function blame(StaticPageModel $pageModel,CharitableModel $charitableModel){
        $type = __FUNCTION__;
        $page = $pageModel->find(4);

        $charitable = $charitableModel->order('year desc','month desc')->get()->toArray();
        include_once(VIEW_ROOT.'Mobile/'.__FUNCTION__.'.php');
    }

}
