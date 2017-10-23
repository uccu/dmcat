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
    
}