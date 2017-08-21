<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;

class StaffController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        

    }

    /* 管理员 */
    function master(){

        View::addData(['getList'=>'/user/admin_master']);
        View::hamlReader('home/list','Admin');
    }

    /*  用户 */
    function user(){

        View::addData(['getList'=>'/user/admin_user']);
        View::hamlReader('home/list','Admin');
    }


    /*  用户 */
    function profit(){

        View::addData(['getList'=>'/money/admin_user']);
        View::hamlReader('home/list','Admin');
    }


    /*  律师 */
    function lawyer(){

        View::addData(['getList'=>'/lawyer/admin_lawyer']);
        View::hamlReader('home/list','Admin');
    }
    
    /* 审批 */
    function apply(){

        View::addData(['getList'=>'/user/admin_apply']);
        View::hamlReader('home/list','Admin');
    }


}