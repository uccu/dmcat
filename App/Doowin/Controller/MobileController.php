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

use App\Doowin\Model\RecruitModel;
use App\Doowin\Model\RecruitTypeModel;
use App\Doowin\Model\MovesModel;
use App\Doowin\Model\UploadModel;
use App\Doowin\Model\ComplaintsModel;

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
        $where['status'] = 1;
        $banner = $bannerModel->order('ord','id')->get()->toArray();
        $newsGroup = $newsGroupModel->where($where)->limit(4)->order('top desc','create_time desc')->get()->toArray();
        $newsHot = $newsHotModel->where($where)->limit(4)->order('top desc','create_time desc')->get()->toArray();
        $newsMedia = $newsMediaModel->where($where)->limit(4)->order('top desc','create_time desc')->get()->toArray();
        $newsVideo = $newsVideoModel->where($where)->limit(2)->order('top desc','create_time desc')->get()->toArray();
        $homeM = $homeMModel->limit(5)->order('id')->get()->toArray();
        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }

    function inNews(NewsGroupModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '集团要闻';
        $limit = 16;
        $where['status'] = 1;
        $list = $model->where($where)->page($page,$limit)->order('top desc','create_time desc')->get()->toArray();
        $max = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
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
        $where['status'] = 1;
        $list = $model->where($where)->page($page,$limit)->order('top desc','create_time desc')->get()->toArray();
        $max = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
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
        $where['status'] = 1;
        $list = $model->where($where)->page($page,$limit)->order('top desc','create_time desc')->get()->toArray();
        $max = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
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
        $where['status'] = 1;
        if($video_type)$where['type']=$video_type;
        $newsVideoType = $newsVideoTypeModel->order('ord','id')->get()->toArray();
        $newsVideo = $newsVideoModel->where($where)->page($page,$limit)->order('top desc','create_time desc')->get()->toArray();
        $where2 = $where;
        $where2['banner'] = 1;
        $banner = $newsVideoModel->where($where2)->order('top desc','create_time desc')->get()->toArray();
        $max = $newsVideoModel->select('COUNT(*) as c','RAW')->where($where)->find()->c;

        include_once(VIEW_ROOT.'Mobile/'. __FUNCTION__ .'.php');

    }
    function videoPlay(NewsVideoModel $newsVideoModel,$id = 0){
        
        
        
        $info = $newsVideoModel->find($id);

        $newsVideo = $newsVideoModel->order('create_time desc')->limit(4)->get()->toArray();
        

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

        $chairmanPicture = $chairmanPictureModel->order('top desc','id desc')->get()->toArray();

        $pic_ = [];

        foreach($chairmanPicture as $k=>$v){

            if($chairmanPicture[$k]->pic){
                $pics = explode(';',$chairmanPicture[$k]->pic);
                
                $chairmanPicture[$k]->pic2Array = $pics;
                $chairmanPicture[$k]->count = count($chairmanPicture[$k]->pic2Array);
                foreach($pics as &$v2)$v2 = Func::fullPicAddr( $v2 );
                $chairmanPicture[$k]->picArray = implode(';',$pics);
                $pic_[$k] = $chairmanPicture[$k]->picArray;
                $chairmanPicture[$k]->first = $chairmanPicture[$k]->pic2Array[0];
            }

        }
        foreach($chairmanPicture as $k=>$v){

            $pic__ = $pic_;
            $chairmanPicture[$k]->picArray = implode(';',array_slice($pic__,$k)).($k?';'.implode(';',array_slice($pic__,0,$k)):'');

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


    function newWorld(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇宝贝广场';
        $page = $pageModel->find(5);
        include_once(VIEW_ROOT.'Mobile/Domain_wandaSquare.php');

    }
    function wandaSquare(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇万达广场';
        $page = $pageModel->find(6);
        include_once(VIEW_ROOT.'Mobile/Domain_wandaSquare.php');

    }
    function newCity(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇特色小镇';
        $page = $pageModel->find(7);
        include_once(VIEW_ROOT.'Mobile/Domain_wandaSquare.php');

    }
    function finance(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇金融';
        $page = $pageModel->find(8);
        include_once(VIEW_ROOT.'Mobile/Domain_wandaSquare.php');

    }
    function edu(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇教育';
        $page = $pageModel->find(9);
        include_once(VIEW_ROOT.'Mobile/Domain_wandaSquare.php');

    }
    function logistics(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇物流';
        $page = $pageModel->find(10);
        include_once(VIEW_ROOT.'Mobile/Domain_wandaSquare.php');

    }


    function recruit(RecruitModel $recruitModel,RecruitTypeModel $recruitTypeModel,$page = 1){
        
        $type = __FUNCTION__;
        $name = '德汇招聘';
        $recruitType = $recruitTypeModel->order('ord')->get()->toArray();
        $typez = Request::getInstance()->cookie('recruit_type',0);
        if(!$typez)$typez = $recruitType[0]->id;
        $limit = 16;
        $where['type'] = $typez;
        $recruit = $recruitModel->where($where)->order('top desc','time desc')->page($page,$limit)->get()->toArray();
        $max = $recruitModel->select('COUNT(*) as c','RAW')->where($where)->find()->c;

        include_once(VIEW_ROOT.'Mobile/recruit.php');

    }
    function legalNotices(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '法律声明';
        $page = $pageModel->find(11);
        include_once(VIEW_ROOT.'Mobile/legalNotices.php');

    }
    function moves(MovesModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '招标及公告';
        $limit = 16;
        $list = $model->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'Mobile/moves.php');

    }
    function movesInfo(MovesModel $model,$id = 0){
        
        $type = 'moves';
        $name = '招标及公告';
        $info = $model->find($id);
        !$info && Response::getInstance()->r302('/404.html');

        $files = \str_replace(';',',',$info->file);
        $files = UploadModel::getInstance()->where('id IN (%i)',$files)->get()->toArray();

        $namee = '';
        foreach($files as $k=>$file){
            $namee .= ($k+1).'、'.$file->name;
            $down .= '<a href="/download/file?id='.$file->id.'">'.$file->name.'</a><br>';
        }

        include_once(VIEW_ROOT.'Mobile/movesInfo.php');

    }
    function complaints(){
        
        $type = __FUNCTION__;
        $name = '投诉及建议';
        include_once(VIEW_ROOT.'Mobile/complaints.php');

    }
    # 投诉与建议
    function send($date,$content,$requires,$name,$sex,$mobile,$phone,ComplaintsModel $model){

        $data['date'] = $date;
        $data['content'] = $content;
        $data['requires'] = $requires;
        $data['name'] = $name;
        $data['sex'] = 2?'先生':'女士';
        $data['mobile'] = $mobile;
        $data['phone'] = $phone;
        $data['create_time'] = TIME_NOW;
        $model->set($data)->add();

        AJAX::success();


    }

    function searcher(){

        include_once(VIEW_ROOT.'Mobile/searcher.php');

    }
}
