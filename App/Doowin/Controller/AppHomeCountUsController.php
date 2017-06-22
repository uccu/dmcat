<?php

namespace App\Doowin\Controller;
use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

use App\Doowin\Model\StaticPageModel;
use App\Doowin\Model\RecruitModel;
use App\Doowin\Model\RecruitTypeModel;
use App\Doowin\Model\MovesModel;
use App\Doowin\Model\UploadModel;



require_once(BASE_ROOT.'App/Doowin/Middleware/Lang.php');

class AppHomeCountUsController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        
    }

    function getPageLink($a,$b,$c='',$d = 16){

        return Func::getPageLink($a,$b,$c,$d);
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

        include_once(VIEW_ROOT.'App/CountUs_recruit.php');

    }
    function legalNotices(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '法律声明';
        $page = $pageModel->find(11);
        include_once(VIEW_ROOT.'App/CountUs_legalNotices.php');

    }
    function moves(MovesModel $model,$page = 1){
        
        $type = __FUNCTION__;
        $name = '招标公告';
        $limit = 16;
        $list = $model->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $max = $model->select('COUNT(*) as c','RAW')->find()->c;
        include_once(VIEW_ROOT.'App/CountUs_moves.php');

    }
    function movesInfo(MovesModel $model,$id = 0){
        
        $type = 'moves';
        $name = '招标公告';
        $info = $model->find($id);
        !$info && Response::getInstance()->r302('/404.html');

        $files = \str_replace(';',',',$info->file);
        $files = UploadModel::getInstance()->where('id IN (%i)',$files)->get()->toArray();

        $namee = '';
        foreach($files as $k=>$file){
            $namee .= ($k+1).'、'.$file->name;
            $down .= '<a href="/download/file?id='.$file->id.'">'.$file->name.'</a><br>';
        }

        include_once(VIEW_ROOT.'App/CountUs_movesInfo.php');

    }
    function complaints(){
        
        $type = __FUNCTION__;
        $name = '投诉及建议';
        include_once(VIEW_ROOT.'App/CountUs_complaints.php');

    }

    
    
}
