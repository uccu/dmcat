<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;

# Model
use App\Lawyer\Model\VisaWorkModel;
use App\Lawyer\Model\VisaFamilyModel;
use App\Lawyer\Model\VisaRefuseModel;
use App\Lawyer\Model\VisaTravelModel;
use App\Lawyer\Model\VisaMarryModel;
use App\Lawyer\Model\VisaGraduateModel;
use App\Lawyer\Model\VisaStudentModel;
use App\Lawyer\Model\VisaPerpetualModel;


class VisaController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }


    function submit($lawyer_id,$type){
        
    }

    # 工作签证
    function getWork(VisaWorkModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updWork(VisaWorkModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 家庭团聚签证
    function getFamily(VisaFamilyModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updFamily(VisaFamilyModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 拒签上诉
    function getRefuse(VisaRefuseModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updRefuse(VisaRefuseModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 旅游签证
    function getTravel(VisaTravelModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updTravel(VisaTravelModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 配偶签证
    function getMarry(VisaMarryModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updMarry(VisaMarryModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;

        $paths = Func::uploadFiles();
        
        $data['pic'] = implode(',',$paths);
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 学生毕业签证
    function getGraduate(VisaGraduateModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updGraduate(VisaGraduateModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 学生签证/陪读
    function getStudent(VisaStudentModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updStudent(VisaStudentModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 学生签证/陪读
    function getPerpetual(VisaPerpetualModel $model){
        
        !$this->L->id && AJAX::error('请登录！');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updPerpetual(VisaPerpetualModel $model){

        !$this->L->id && AJAX::error('请登录！');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

}