<?php

namespace App\School\Controller;


use Controller;
use Response;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Model\CommentModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class TeacherController extends Controller{


    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* 每日点评 */

    function add_comment($id,$month,$day,CommentModel $model){

        $data = Request::getInstance()->request($model->field);

        unset($data['id']);

        $data['create_time'] = TIME_NOW;

        $where['student_id'] = $id;
        $where['month'] = $month;
        $where['day'] = $day;

        $las = $model->where($where)->find();

        if($las)AJAX::error('EXIST');


        $model->set($data)->add();

        AJXAX::success();

    }

}