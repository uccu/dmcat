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

# 数据模型
use App\App\Model\ClinicModel;
use App\App\Model\DoctorModel;
use Model; 


class ClinicController extends Controller{


    function __construct(){


    }


    /** 诊所列表
     * list
     * @param mixed $clinicModel 
     * @param mixed $latitude 
     * @param mixed $longitude 
     * @param mixed $page 
     * @param mixed $limit 
     * @param mixed $sort_type 
     * @param mixed $distict_id 
     * @return mixed 
     */
    function lists(ClinicModel $clinicModel,$latitude = 0, $longitude = 0,$page = 1,$limit = 10,$sort_type,$distict_id = 0){

        // $latitude = 31.10079300;
        // $longitude = 121.16671300;

        if($distict_id){

            $where['distict_id'] = $distict_id;
        }

        $list = $clinicModel->select(['*,ABS(%F-%f) + ABS(%F-%f) AS `mul`','latitude',$latitude,'longitude',$longitude],'RAW')->order('mul desc','RAW')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){

            $v->distance = Func::getSDistance($v->latitude,$v->longitude,$latitude,$longitude);
        }

        $out['list'] = $list;
        AJAX::success($out);
        
    }


    /** 获取诊所详情
     * info
     * @param mixed $clinicModel 
     * @param mixed $doctorModel 
     * @param mixed $id 
     * @param mixed $latitude 
     * @param mixed $longitude 
     * @return mixed 
     */
    function info(ClinicModel $clinicModel,DoctorModel $doctorModel ,$id,$latitude = 0, $longitude = 0){

        // $latitude = 31.10079300;
        // $longitude = 121.16671300;
        
        $info = $clinicModel->select(['*,ABS(%F-%f) + ABS(%F-%f) AS `mul`','latitude',$latitude,'longitude',$longitude],'RAW')->find($id);

        !$info && AJAX::error('诊所不存在！');

        $info->distance = Func::getSDistance($info->latitude,$info->longitude,$latitude,$longitude);

        $where['clinic_id'] = $info->id;
        $where['active'] = 1;

        $list = $doctorModel->select('id','avatar','phone','experience','name','status','skill')->where($where)->get()->toArray();



        $out['info'] = $info;
        $out['list'] = $list;
        AJAX::success($out);

    }
    
    /** 医生详情
     * doctorInfo
     * @param mixed $doctorModel 
     * @param mixed $id 
     * @return mixed 
     */
    function doctorInfo(DoctorModel $doctorModel ,$id){

        $info = $doctorModel->select('id','avatar','active','introduce','name','status','skill')->find($id);

        !$info && AJAX::error('医生不存在！');
        !$info->active && AJAX::error('该医生已被禁用！');

        $out['info'] = $info;
        AJAX::success($out);

    }



}