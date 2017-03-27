<?php

namespace App\School\Controller;


use AJAX;
use Controller;
use App\School\Model\UserModel;



class UserController extends Controller{


    function __construct(){

        

    }


    /* 通过用户ID判断用户是否存在 */
    function exist($id = 0){

        $user = UserMode::getInstance()->find($id);
        $outData['exist'] = $user ? true : false;
        AJAX::success($outData);
    }


    


}