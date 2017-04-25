<?php

namespace App\School\Controller;


use Controller;
use Response;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;
use Model;

class ParentController extends Controller{



    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }

    function index(){

        View::hamlReader('parent/'.__FUNCTION__,'App');
    }
    

    function add_school_message($message){

        $this->L->check_type(1);

        if(!$message)AJAX::error_i18n('param_error');

        $data['parent_id'] = $this->L->id;
        $data['message'] = $message;
        $data['create_time'] = TIME_NOW;

        Model::getInstance('school_message')->set($data)->add();

        AJAX::success();


    }

    function get_notice_list(){

        $list = Model::getInstance('notice')->selectExcept('content')->where(['isshow'=>1])->order('create_time','DESC')->get()->toArray();

        foreach($list as &$v){
            $v->create_date = date('Y-m-d',$v->create_time);
        }

        $out['list'] = $list;

        AJAX::success($out);

    }


}