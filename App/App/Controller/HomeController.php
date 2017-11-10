<?php

namespace App\App\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\App\Middleware\L;
use App\App\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;
use View;

# 数据模型
use App\App\Model\UserModel;
use App\App\Model\DoctorModel;
use App\App\Model\AreaModel;
use App\App\Model\TagModel;
use Model; 


class HomeController extends Controller{


    function __construct(){


    }

    /** 检查手机号的用户类型
     * checkUserType
     * @param mixed $phone 
     * @param mixed $userModel 
     * @param mixed $doctorModel 
     * @return mixed 
     */
    function checkUserType($phone,UserModel $userModel,DoctorModel $doctorModel){

        $doctorModel->where(['phone'=>$phone])->find() && AJAX::success(['type'=>'doctor']);
        $userModel->where(['phone'=>$phone])->find() && AJAX::success(['type'=>'user']);
        AJAX::success(['type'=>'no']);
    }

    function upAvatar(){

        $out['path'] = Func::uploadFiles('file',100,100);
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }

    function uploadPic(){

        $out['path'] = Func::uploadFiles('file');
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);

    }
    function uploadFile(){
        
        $id = Func::upload('file');
        if(!$id)AJAX::error('no file');
        $out['path'] = $id;
        $out['fpath'] = '/pic/file.jpg';
        $out['apath'] = Func::fullPicAddr('file.jpg');
        AJAX::success($out);
    }

    /** 获取省市
     * area
     * @param mixed $id 
     * @param mixed $areaModel 
     * @return mixed 
     */
    function area($id,AreaModel $areaModel){

        if(!$id){

            $list = $areaModel->where(['parent_id'=>0])->order('pinyin')->get_field('areaName','id')->toArray();
        }else{

            $list = $areaModel->where(['parent_id'=>$id])->order('pinyin')->get_field('areaName','id')->toArray();
        }
        $out['list'] = $list;
        AJAX::success($out);
    }
    function area2($id,AreaModel $areaModel){

        if(!$id){

            $list = $areaModel->where(['parent_id'=>0])->where(['areaName'=>'福建省'])->order('pinyin')->get()->toArray();
        }else{

            $list = $areaModel->where(['parent_id'=>$id])->order('pinyin')->get()->toArray();
        }
        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 标签列表
     * tags
     * @param mixed $model 
     * @return mixed 
     */
    function tags(TagModel $model){

        $list = $model->get()->toArray();

        $out['list'] = $list;
        AJAX::success($out);
    }
    
}