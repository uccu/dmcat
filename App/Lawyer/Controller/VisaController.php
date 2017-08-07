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





}