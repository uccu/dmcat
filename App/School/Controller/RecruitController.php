<?php

namespace App\School\Controller;


use Controller;
use View;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

use App\School\Model\RecruitModel;

class RecruitController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    function add($name,$time,$address,$number,$comment,RecruitModel $model){

        $data = Request::getInstance()->post(['name','time','address','number','comment']);
        count($data) != 4 && AJAX::error_i18n('param_error');

        $succ = $model->set($data)->add()->getStatus();
        !$succ && AJAX::error_i18n('param_error');
        
        AJAX::success();

    }


}