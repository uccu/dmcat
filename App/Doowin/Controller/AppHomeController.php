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
        NewsVideoModel $newsVideoModel
        ){

        $banner = $bannerModel->order('ord','id')->get()->toArray();
        $newsGroup = $newsGroupModel->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsHot = $newsHotModel->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsMedia = $newsMediaModel->limit(4)->order('top desc','id desc')->get()->toArray();
        $newsVideo = $newsVideoModel->limit(2)->order('top desc','id desc')->get()->toArray();
        include_once(VIEW_ROOT.'App/Index_index.php');

    }


}
