<?php

namespace App\Doowin\Controller;
use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

use App\Doowin\Model\HomeBannerModel;
use App\Doowin\Model\NewsGroupModel;
use App\Doowin\Model\NewsHotModel;
use App\Doowin\Model\NewsMediaModel;
use App\Doowin\Model\NewsVideoModel;
use App\Doowin\Model\HomeMModel;

require_once(BASE_ROOT.'App/Doowin/Middleware/Lang.php');

class AppHomeController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        
    }

    function Index(
        HomeBannerModel $bannerModel,
        NewsGroupModel $newsGroupModel,
        NewsHotModel $newsHotModel,
        NewsMediaModel $newsMediaModel,
        NewsVideoModel $newsVideoModel,
        HomeMModel $homeMModel
        ){

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            header('Location:/mobile/index');exit();
        }
                
        
        $where['status'] = 1;
        $banner = $bannerModel->order('ord','id')->get()->toArray();
        $newsGroup = $newsGroupModel->where($where)->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsHot = $newsHotModel->where($where)->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsMedia = $newsMediaModel->where($where)->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsVideo = $newsVideoModel->where($where)->limit(2)->order('top desc','id desc')->get()->toArray();
        $homeM = $homeMModel->limit(5)->order('id')->get()->toArray();
        include_once(VIEW_ROOT.'App/Index_index.php');

    }


}
