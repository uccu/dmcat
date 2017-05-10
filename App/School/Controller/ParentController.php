<?php

namespace App\School\Controller;


use Controller;
use Response;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Model\UserStudentModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;
use Request;
use Model;
use App\School\Model\NoticeConfirmModel;

class ParentController extends Controller{



    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }

    function index(){

        $id = $this->L->id;

        include VIEW_ROOT.'App/parent/'.__FUNCTION__.'.php';

        // View::hamlReader('parent/'.__FUNCTION__,'App');
    }


    function profile($id){

        $stu_id = $id;
        $id = $this->L->id;
        include VIEW_ROOT.'App/parent/'.__FUNCTION__.'.php';
    }

    function children($id){

        include VIEW_ROOT.'App/parent/'.__FUNCTION__.'.php';
    }
    function tijian($id){

        include VIEW_ROOT.'App/parent/'.__FUNCTION__.'.php';
    }
    function leave($id){

        include VIEW_ROOT.'App/parent/'.__FUNCTION__.'.php';
    }
    function ask($id){

        include VIEW_ROOT.'App/parent/'.__FUNCTION__.'.php';
    }

    function get_my_info($id ,UserModel $model){

        // $id = $this->L->id;

        !$id && AJAX::success(['info'=>[]]);
        $info = $model->select('type','avatar','raw_password>password','phone','email')->find($id);
        if(!$info)AJAX::error('用户不存在/Not Exist User');
        if($info->type != '1')AJAX::error('用户非家长/Not Parent');

        
        if($info->avatar)$info->avatar = Func::fullPicAddr($info->avatar);
        
        $out['info'] = $info;

        $out['student'] = UserStudentModel::getInstance()->select('id','studentInfo.name','studentInfo.name_en')->where(['user_id'=>$id])->get()->toArray();
        !$info && AJAX::error_i18n('no_data');
        $out['lang'] = $this->lang->language;

        AJAX::success($out);

    }
    

    function add_school_message($message){

        $this->L->check_type(1);

        if(!$message)AJAX::error_i18n('param_error');

        $data['user_id'] = $this->L->id;
        $data['message'] = $message;
        $data['create_time'] = TIME_NOW;

        Model::getInstance('school_message')->set($data)->add();

        AJAX::success();

    }


    function child($id = 0){

        $out['list'] = UserStudentModel::getInstance()->select('student_id>id','studentInfo.name','studentInfo.name_en','studentInfo.avatar')->where(['user_id'=>$id])->get()->toArray();

        foreach($out['list'] as &$v){
            $v->fullAvatar = Func::fullPicAddr($v->avatar);
        }

        AJAX::success($out);
    }

    function get_notice_list(){

        $list = Model::getInstance('notice')->selectExcept('content')->where(['isshow'=>1])->order('create_time','DESC')->get()->toArray();

        foreach($list as &$v){
            $v->create_date = date('Y-m-d',$v->create_time);
        }

        $out['list'] = $list;

        AJAX::success($out);

    }


    function get_notice($id=0,$student_id = 0,NoticeConfirmModel $model){

        (!$id || !$student_id) && AJAX::error_i18n('param_error');

        $info = Model::getInstance('notice')->find($id);

        $info->create_date = date('Y-m-d',$v->create_time);

        if($info->need_confirm){
            $data['student_id'] = $student_id;
            $data['notice_id'] = $id;
            if(!$model->where($data)->find()){
                $data['create_time'] = TIME_NOW;
                $model->set($data)->add(true);
            }
        }
        

        $out['info'] = $info;

        AJAX::success($out);

    }


    function upd($id = 0,UserModel $model){

        $id = $this->L->id;

        $data = Request::getInstance()->request(['email','phone','raw_password','avatar']);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_user_exist');

        if(!$data['raw_password'])unset($data['raw_password']);
        elseif($data['raw_password'] && $info->raw_password !== $data['raw_password'])
            $data['password'] = sha1($this->salt.md5($data['raw_password']));

        $model->set($data)->save($id);
        

        AJAX::success();

    }

    function student_upd($id = 0,StudentModel $model){

        $data = Request::getInstance()->request(['avatar','allergy']);

        $info = $model->find($id);
        !$info && AJAX::error('没有该学生/no student');

        if(!$data['avatar'])unset($data['avatar']);

        $model->set($data)->save($id);
        

        AJAX::success();

    }



    


}