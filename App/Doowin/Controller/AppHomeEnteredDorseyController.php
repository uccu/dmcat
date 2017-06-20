<?php

namespace App\Doowin\Controller;
use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

use App\Doowin\Model\StaticPageModel;
use App\Doowin\Model\IntroductionProductModel;
use App\Doowin\Model\DevelopModel;
use App\Doowin\Model\ChairmanPictureModel;
use App\Doowin\Model\HonorModel;
use App\Doowin\Model\CharitableModel;


require_once(BASE_ROOT.'App/Doowin/Middleware/Lang.php');

class AppHomeEnteredDorseyController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        
    }

    function profile(StaticPageModel $pageModel,IntroductionProductModel $introductionProductModel){
        
        $type = __FUNCTION__;

        $page = $pageModel->find(1);
        $introductionProduct = $introductionProductModel->order('top desc','id desc')->get()->toArray();

        include_once(VIEW_ROOT.'App/EnteredDorsey_'.__FUNCTION__.'.php');

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


        include_once(VIEW_ROOT.'App/EnteredDorsey_'.__FUNCTION__.'.php');

    }
    

    function develop($year = 0,DevelopModel $model){
        $type = __FUNCTION__;
        $yearM = date('Y');
        $year = floor($year);
        if(!$year)$year = $yearM;
        $where['year'] = $year;
        $list = $model->where($where)->order('month desc')->get()->toArray();
        include_once(VIEW_ROOT.'App/EnteredDorsey_'.__FUNCTION__.'.php');

    }
    function culture(StaticPageModel $pageModel){
        $type = __FUNCTION__;
        $page = $pageModel->find(3);
        include_once(VIEW_ROOT.'App/EnteredDorsey_'.__FUNCTION__.'.php');
    }
    function honor($year = 0,HonorModel $model){
        $type = __FUNCTION__;
        $yearM = date('Y');
        $year = floor($year);
        if(!$year)$year = $yearM;
        $where['year'] = $year;
        $list = $model->where($where)->order('month desc')->get()->toArray();
        include_once(VIEW_ROOT.'App/EnteredDorsey_'.__FUNCTION__.'.php');

    }

    function blame(StaticPageModel $pageModel,CharitableModel $charitableModel){
        $type = __FUNCTION__;
        $page = $pageModel->find(4);

        $charitable = $charitableModel->order('year desc','month desc')->get()->toArray();
        include_once(VIEW_ROOT.'App/EnteredDorsey_'.__FUNCTION__.'.php');
    }
}
