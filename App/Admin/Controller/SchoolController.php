<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;

class SchoolController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        

    }

    /* 学校管理 */
    function school(){

        View::addData(['getList'=>'/school/admin_school']);
        View::hamlReader('home/list','Admin');
    }


    function user_school($id){

        View::addData(['getList'=>'/school/admin_user_school?id='.$id]);
        View::hamlReader('home/list','Admin');

    }

    


}